<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AdminAssistantController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => ['required', 'string', 'max:500'],
        ]);

        $text = trim($validated['text']);
        $normalized = Str::lower((string) preg_replace('/\s+/', ' ', $text));

        $intent = $this->resolveIntent($normalized);
        if ($intent !== null) {
            return response()->json($intent);
        }

        $clinicReply = $this->answerClinicInfo($normalized);
        if ($clinicReply !== null) {
            return response()->json([
                'type' => 'answer',
                'message' => $clinicReply,
                'source' => 'local',
            ]);
        }

        $medicalReply = $this->answerMedicalGuidance($normalized);
        if ($medicalReply !== null) {
            return response()->json([
                'type' => 'answer',
                'message' => $medicalReply,
                'source' => 'local',
            ]);
        }

        $aiReply = $this->askOpenAi($text);
        if ($aiReply !== null) {
            return response()->json([
                'type' => 'answer',
                'message' => $aiReply,
                'source' => 'ai',
            ]);
        }

        return response()->json([
            'type' => 'answer',
            'message' => 'I can help with commands like "generate MAR", "open appointments", "open inventory", and basic symptom triage guidance. This is guidance only and not a diagnosis.',
            'source' => 'fallback',
        ]);
    }

    private function resolveIntent(string $text): ?array
    {
        $month = $this->parseMonthFromText($text);
        $basePath = $this->resolveWorkspaceBasePath();
        $isAdminLike = $this->isAdminLikeUser();

        if ($this->containsAny($text, ['generate mar', 'open mar', 'show mar', 'medical accomplishment'])) {
            $url = url($basePath . '/reports/mar?month=' . $month);
            return $this->redirectIntent('Opening MAR report.', $url);
        }

        if ($this->containsAny($text, ['manage mar', 'edit mar conditions'])) {
            if (!$isAdminLike) {
                return [
                    'type' => 'answer',
                    'message' => 'Manage MAR is restricted to Admin and Super Admin accounts.',
                ];
            }
            $url = url('/admin/reports/manage-mar?month=' . $month);
            return $this->redirectIntent('Opening Manage MAR.', $url);
        }

        if ($this->containsAny($text, ['print mar', 'export mar'])) {
            $url = url($basePath . '/reports/print-reports?type=mar&month=' . $month);
            return $this->redirectIntent('Generating MAR print report.', $url);
        }

        if ($this->containsAny($text, ['print inventory', 'export inventory'])) {
            $url = url($basePath . '/reports/print-reports?type=inventory&month=' . $month);
            return $this->redirectIntent('Generating inventory print report.', $url);
        }

        if ($this->containsAny($text, ['print appointment', 'export appointment'])) {
            $url = url($basePath . '/reports/print-reports?type=appointment&month=' . $month);
            return $this->redirectIntent('Generating appointment print report.', $url);
        }

        if ($this->containsAny($text, ['open dashboard', 'go to dashboard', 'show dashboard'])) {
            return $this->redirectIntent('Opening dashboard.', url($basePath . '/dashboard'));
        }

        if ($this->containsAny($text, ['open appointment', 'go to appointment', 'show appointment'])) {
            return $this->redirectIntent('Opening appointments.', url($basePath . '/appointments'));
        }

        if ($this->containsAny($text, ['open inventory', 'go to inventory', 'show inventory'])) {
            return $this->redirectIntent('Opening inventory.', url($basePath . '/inventory'));
        }

        if ($this->containsAny($text, ['open reports', 'go to reports', 'show reports'])) {
            return $this->redirectIntent('Opening reports.', url($basePath . '/reports'));
        }

        if ($this->containsAny($text, ['open settings', 'go to settings', 'show settings'])) {
            if (!$isAdminLike) {
                return [
                    'type' => 'answer',
                    'message' => 'Settings access is restricted to Admin and Super Admin accounts.',
                ];
            }
            return $this->redirectIntent('Opening settings.', url('/admin/settings'));
        }

        if ($this->containsAny($text, ['open walk in', 'open walkin', 'new walk in'])) {
            return $this->redirectIntent('Opening walk-in management.', url($basePath . '/walkin'));
        }

        if ($this->containsAny($text, ['open export hub', 'reports hub', 'export reports'])) {
            return $this->redirectIntent('Opening export hub.', url($basePath . '/reports/export-hub'));
        }

        if ($this->containsAny($text, ['inventory summary'])) {
            return $this->redirectIntent('Opening inventory summary.', url($basePath . '/reports/inventory-summary'));
        }

        return null;
    }

    private function answerClinicInfo(string $text): ?string
    {
        $settings = Setting::first();
        $clinicName = $settings?->clinic_name ?: 'PUP Clinic';
        $location = $settings?->clinic_location ?: 'clinic office';
        $open = $settings?->open_time ?: '08:00';
        $close = $settings?->close_time ?: '17:00';

        if ($this->containsAny($text, ['clinic hours', 'open time', 'closing time', 'what time are you open', 'what time do you close'])) {
            return "Clinic hours for {$clinicName}: {$open} to {$close}. Please confirm holidays or special schedules at the front desk.";
        }

        if ($this->containsAny($text, ['clinic location', 'where is the clinic', 'where is clinic'])) {
            return "Clinic location: {$location}.";
        }

        if ($this->containsAny($text, ['how to book', 'book appointment', 'set appointment'])) {
            return 'For booking: open the Student booking page, choose service/date/time, then submit. For walk-ins, use the Walk-in module in Admin.';
        }

        return null;
    }

    private function answerMedicalGuidance(string $text): ?string
    {
        $emergencyKeywords = [
            'chest pain',
            'difficulty breathing',
            'shortness of breath',
            'severe bleeding',
            'unconscious',
            'fainting',
            'seizure',
            'stroke',
            'one side weak',
            'suicidal',
            'anaphylaxis',
        ];

        if ($this->containsAny($text, $emergencyKeywords)) {
            return 'This may be an emergency. Call emergency services now (911 in the US) or go to the nearest ER immediately. Do not wait for online guidance.';
        }

        if ($this->containsAny($text, ['fever', 'lagnat', 'cough', 'sore throat', 'flu', 'cold'])) {
            return 'Possible causes include viral respiratory infection. Initial care: rest, hydration, monitor temperature, and use age-appropriate fever medicine if needed. Seek clinic assessment today if fever lasts more than 48 hours, breathing worsens, oxygen is low, or there is chest pain. This is triage guidance only, not a diagnosis.';
        }

        if ($this->containsAny($text, ['headache', 'migraine', 'dizziness'])) {
            return 'Common causes include tension headache, dehydration, lack of sleep, or migraine. Initial care: hydrate, rest in a quiet room, and monitor blood pressure if available. Seek urgent care if severe sudden headache, vision/speech changes, confusion, weakness, or repeated vomiting occurs. This is triage guidance only, not a diagnosis.';
        }

        if ($this->containsAny($text, ['stomach pain', 'abdominal pain', 'diarrhea', 'vomiting', 'food poisoning'])) {
            return 'Possible causes include gastroenteritis or food-related irritation. Initial care: oral rehydration, light meals, and avoid oily/spicy food. Seek clinic or ER if severe abdominal pain, bloody stool/vomit, persistent vomiting, high fever, or signs of dehydration occur. This is triage guidance only, not a diagnosis.';
        }

        if ($this->containsAny($text, ['high blood pressure', 'hypertension', 'bp', 'blood pressure'])) {
            return 'Repeat blood pressure after 5 to 10 minutes rest with proper cuff size. Limit caffeine/smoking before reading. Seek urgent care if BP is very high with chest pain, severe headache, shortness of breath, or neurologic symptoms. This is triage guidance only, not a diagnosis.';
        }

        if ($this->containsAny($text, ['urine pain', 'painful urination', 'uti', 'frequent urination'])) {
            return 'Possible urinary infection should be assessed in clinic for urine testing and treatment plan. Hydrate well and avoid delaying urination. Seek urgent care if fever, flank pain, vomiting, pregnancy, or blood in urine occurs. This is triage guidance only, not a diagnosis.';
        }

        if ($this->containsAny($text, ['wound', 'cut', 'burn', 'injury'])) {
            return 'For minor wounds: clean with running water, apply gentle pressure if bleeding, and cover with clean dressing. For burns: cool with running water for 20 minutes, no ice. Seek urgent care for deep wounds, uncontrolled bleeding, electrical/chemical burns, or signs of infection. This is triage guidance only, not a diagnosis.';
        }

        if ($this->containsAny($text, ['diagnosis', 'what illness', 'what disease', 'what do i have'])) {
            return 'I can provide symptom triage but not a definitive diagnosis. For a better assessment, share age, main symptoms, duration, temperature, blood pressure, current medicines, and red-flag symptoms. A clinician should confirm diagnosis in person.';
        }

        return null;
    }

    private function askOpenAi(string $text): ?string
    {
        $apiKey = config('services.openai.api_key') ?: env('OPENAI_API_KEY');
        $model = config('services.openai.model') ?: env('OPENAI_MODEL', 'gpt-4o-mini');

        if (empty($apiKey)) {
            return null;
        }

        try {
            $response = Http::timeout(18)
                ->withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'temperature' => 0.2,
                    'max_tokens' => 260,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a campus clinic triage assistant for admin use. Give concise, safe triage guidance. Do not provide a definitive diagnosis. Always mention urgent red flags and when to escalate to emergency care.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $text,
                        ],
                    ],
                ]);

            if (!$response->successful()) {
                return null;
            }

            $content = trim((string) data_get($response->json(), 'choices.0.message.content'));
            return $content !== '' ? $content : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function redirectIntent(string $message, string $url): array
    {
        return [
            'type' => 'action',
            'message' => $message,
            'action' => [
                'kind' => 'redirect',
                'url' => $url,
            ],
        ];
    }

    private function containsAny(string $text, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (Str::contains($text, $needle)) {
                return true;
            }
        }
        return false;
    }

    private function parseMonthFromText(string $text): string
    {
        if (preg_match('/\b(20\d{2})-(0[1-9]|1[0-2])\b/', $text, $matches) === 1) {
            return $matches[1] . '-' . $matches[2];
        }

        $monthMap = [
            'jan' => 1, 'january' => 1,
            'feb' => 2, 'february' => 2,
            'mar' => 3, 'march' => 3,
            'apr' => 4, 'april' => 4,
            'may' => 5,
            'jun' => 6, 'june' => 6,
            'jul' => 7, 'july' => 7,
            'aug' => 8, 'august' => 8,
            'sep' => 9, 'sept' => 9, 'september' => 9,
            'oct' => 10, 'october' => 10,
            'nov' => 11, 'november' => 11,
            'dec' => 12, 'december' => 12,
        ];

        $month = (int) now()->month;
        $year = (int) now()->year;

        foreach ($monthMap as $name => $value) {
            if (preg_match('/\b' . preg_quote($name, '/') . '\b/', $text) === 1) {
                $month = $value;
                break;
            }
        }

        if (preg_match('/\b(20\d{2})\b/', $text, $yearMatch) === 1) {
            $year = (int) $yearMatch[1];
        }

        return sprintf('%04d-%02d', $year, $month);
    }

    private function resolveWorkspaceBasePath(): string
    {
        $role = strtolower((string) (optional(auth()->user())->user_role ?? ''));
        return $role === 'student_assistant' ? '/assistant' : '/admin';
    }

    private function isAdminLikeUser(): bool
    {
        $role = strtolower((string) (optional(auth()->user())->user_role ?? ''));
        return in_array($role, ['admin', 'super_admin'], true);
    }
}

