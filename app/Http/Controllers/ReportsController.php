<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MedicalConditions;
use App\Models\Category;
use App\Models\Consultation;
use App\Models\AppointmentFeedback;
use App\Models\Item;
use App\Models\HealthProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportsController extends Controller
{
    public function healthFormsReport(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $monthFilter = trim((string) $request->query('month', now()->format('Y-m')));

        $issuedBaseQuery = HealthProfile::query()
            ->with('user')
            ->where('clearance_status', 'Issued');

        if ($monthFilter !== '') {
            $monthStart = Carbon::parse($monthFilter . '-01')->startOfMonth();
            $monthEnd = (clone $monthStart)->endOfMonth();

            $issuedBaseQuery->where(function ($builder) use ($monthStart, $monthEnd) {
                $builder->whereBetween('verified_at', [$monthStart, $monthEnd])
                    ->orWhere(function ($fallback) use ($monthStart, $monthEnd) {
                        $fallback->whereNull('verified_at')
                            ->whereBetween('created_at', [$monthStart, $monthEnd]);
                    });
            });
        }

        if ($search !== '') {
            $issuedBaseQuery->where(function ($builder) use ($search) {
                $builder->where('course_college', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('course', 'like', "%{$search}%");
                    });
            });
        }

        $issuedFormsCollection = (clone $issuedBaseQuery)
            ->get()
            ->groupBy(function (HealthProfile $form) {
                $course = trim((string) ($form->course_college ?: optional($form->user)->course ?: 'Unspecified Course'));
                return $course !== '' ? $course : 'Unspecified Course';
            })
            ->map(function ($forms, $course) {
                $sortedForms = $forms->sortByDesc(function (HealthProfile $form) {
                    return $form->verified_at ?? $form->created_at;
                })->values();

                $withConditionCount = $forms->where('has_illness', 'Yes')->count();
                $issuedCount = $forms->count();

                return (object) [
                    'course' => $course,
                    'issued_count' => $issuedCount,
                    'with_condition_count' => $withConditionCount,
                    'no_condition_count' => $issuedCount - $withConditionCount,
                    'last_issued_at' => optional($sortedForms->first())->verified_at ?? optional($sortedForms->first())->created_at,
                ];
            })
            ->sortByDesc('issued_count')
            ->values();

        $perPage = 12;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $issuedFormsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $issuedForms = new LengthAwarePaginator(
            $currentItems,
            $issuedFormsCollection->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        $summaryQuery = HealthProfile::query()->where('clearance_status', 'Issued');

        if ($monthFilter !== '') {
            $monthStart = Carbon::parse($monthFilter . '-01')->startOfMonth();
            $monthEnd = (clone $monthStart)->endOfMonth();

            $summaryQuery->where(function ($builder) use ($monthStart, $monthEnd) {
                $builder->whereBetween('verified_at', [$monthStart, $monthEnd])
                    ->orWhere(function ($fallback) use ($monthStart, $monthEnd) {
                        $fallback->whereNull('verified_at')
                            ->whereBetween('created_at', [$monthStart, $monthEnd]);
                    });
            });
        }

        $totalIssued = (clone $summaryQuery)->count();
        $totalCourses = $issuedFormsCollection->count();
        $issuedWithConditions = (clone $summaryQuery)->where('has_illness', 'Yes')->count();
        $topCourse = optional($issuedFormsCollection->first())->course ?? 'No course data yet';

        return view('admin.reports.health-forms', compact(
            'issuedForms',
            'totalIssued',
            'totalCourses',
            'issuedWithConditions',
            'topCourse',
            'search',
            'monthFilter'
        ));
    }

    public function feedbackReport(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $monthFilter = trim((string) $request->query('month', now()->format('Y-m')));

        $query = AppointmentFeedback::query()
            ->with(['appointment', 'user'])
            ->whereNotNull('submitted_at');

        if ($monthFilter !== '') {
            $monthStart = Carbon::parse($monthFilter . '-01')->startOfMonth();
            $monthEnd = (clone $monthStart)->endOfMonth();
            $query->whereBetween('submitted_at', [$monthStart, $monthEnd]);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('feedback', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('student_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('appointment', function ($appointmentQuery) use ($search) {
                        $appointmentQuery->where('service', 'like', "%{$search}%")
                            ->orWhere('type', 'like', "%{$search}%")
                            ->orWhere('user_type', 'like', "%{$search}%");
                    });
            });
        }

        $feedbackItems = (clone $query)
            ->latest('submitted_at')
            ->paginate(12)
            ->through(function (AppointmentFeedback $feedback) {
                $appointment = $feedback->appointment;
                $user = $feedback->user;
                $firstName = trim((string) ($user->first_name ?? ''));
                $lastName = trim((string) ($user->last_name ?? ''));
                $fallbackName = trim((string) ($user->name ?? ''));

                if ($firstName === '' && $fallbackName !== '') {
                    $nameParts = preg_split('/\s+/', $fallbackName) ?: [];
                    $firstName = trim((string) ($nameParts[0] ?? ''));
                    $lastName = trim((string) ($nameParts[count($nameParts) - 1] ?? ''));
                }

                $surnameInitial = $lastName !== '' ? strtoupper(substr($lastName, 0, 1)) . '.' : '';
                $displayName = trim($firstName . ($surnameInitial !== '' ? ' ' . $surnameInitial : ''));
                if ($displayName === '') {
                    $displayName = 'Clinic User';
                }

                $initials = collect(preg_split('/\s+/', $displayName) ?: [])
                    ->filter()
                    ->take(2)
                    ->map(fn ($part) => strtoupper(substr(rtrim($part, '.'), 0, 1)))
                    ->implode('');

                return (object) [
                    'id' => $feedback->id,
                    'name' => $displayName,
                    'initials' => $initials !== '' ? $initials : 'CU',
                    'student_number' => trim((string) ($user->student_number ?? '')),
                    'role' => trim((string) ($appointment->user_type ?? $user->user_role ?? 'User')),
                    'service' => trim((string) ($appointment->service ?? 'General Consultation')),
                    'appointment_type' => trim((string) ($appointment->type ?? '')),
                    'rating' => (int) $feedback->rating,
                    'score_out_of_ten' => number_format(((int) $feedback->rating) * 2, 1),
                    'message' => trim((string) $feedback->feedback),
                    'submitted_at' => $feedback->submitted_at,
                    'time_ago' => optional($feedback->submitted_at)->diffForHumans() ?? 'Recently',
                ];
            });

        $summaryBaseQuery = AppointmentFeedback::query()->whereNotNull('submitted_at');
        if ($monthFilter !== '') {
            $monthStart = Carbon::parse($monthFilter . '-01')->startOfMonth();
            $monthEnd = (clone $monthStart)->endOfMonth();
            $summaryBaseQuery->whereBetween('submitted_at', [$monthStart, $monthEnd]);
        }

        $totalFeedbacks = (clone $summaryBaseQuery)->count();
        $averageRating = round((float) ((clone $summaryBaseQuery)->avg('rating') ?? 0), 1);
        $clinicScore = round($averageRating * 2, 1);
        $recommendedCount = (clone $summaryBaseQuery)->where('rating', '>=', 4)->count();
        $lowRatingCount = (clone $summaryBaseQuery)->where('rating', '<=', 2)->count();

        return view('admin.reports.feedbacks', compact(
            'feedbackItems',
            'totalFeedbacks',
            'clinicScore',
            'recommendedCount',
            'lowRatingCount',
            'search',
            'monthFilter'
        ));
    }

    private function buildInventoryReportData(string $monthFilter)
    {
        $monthStart = Carbon::parse($monthFilter . '-01')->startOfMonth();
        $monthEnd = (clone $monthStart)->endOfMonth();

        $consumedByMedicine = Consultation::query()
            ->select('medicine', DB::raw('SUM(medicine_quantity) as consumed_total'))
            ->whereBetween('consultation_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->whereNotNull('medicine')
            ->where('medicine', '!=', '')
            ->groupBy('medicine')
            ->pluck('consumed_total', 'medicine');

        return Item::query()
            ->orderBy('name')
            ->get()
            ->map(function ($item) use ($consumedByMedicine) {
                $consumed = (int) ($consumedByMedicine[$item->name] ?? 0);
                $item->unit = $item->unit ?: 'pcs';
                $item->consumed = $consumed;
                $item->current_balance = (int) $item->quantity;
                $item->starting_stock = $item->current_balance + $consumed;
                $item->report_category = $item->category === 'Medicine' && $item->medicine_type
                    ? $item->category . ' (' . $item->medicine_type . ')'
                    : $item->category;

                return $item;
            });
    }

    public function marReport(Request $request)
{
    $monthFilter = $request->input('month', date('Y-m')); 
    $year = date('Y', strtotime($monthFilter));
    $month = date('m', strtotime($monthFilter));

    // Gamitin ang consultations table columns directly para sa counting
    $categories = Category::with(['medicalConditions.consultations' => function($query) use ($year, $month) {
        $query->whereYear('consultation_date', $year)
              ->whereMonth('consultation_date', $month);
    }])->get();

    $allConditions = MedicalConditions::with('category')->get();
    $categoryList = Category::all();
    $totalToday = Consultation::whereDate('consultation_date', today())->count();

    return view('admin.reports.mar', [
        'categories' => $categories,
        'allConditions' => $allConditions,
        'categoryList' => $categoryList,
        'month' => $monthFilter,
        'totalToday' => $totalToday
    ]);
}
    // for managing mar
    public function manageMar(Request $request)
{
    $month = $request->get('month', date('Y-m'));
    
    // Para sa dropdown ng categories
    $categoryList = Category::all(); 
    
    // Para sa listahan ng conditions sa table
    $allConditions = MedicalConditions::with('category')->get(); 

    // Iba pang logic para sa reports...
    $categories = Category::with(['medicalConditions.consultations' => function($query) use ($month) {
        $query->where('consultation_date', 'like', $month . '%');
    }])->get();

    return view('admin.reports.manage-mar', compact('categoryList', 'allConditions', 'categories', 'month'));
}

//for changing category
public function update(Request $request, $id)
{
    $request->validate(['category_id' => 'required|exists:categories,id']);
    
    $condition = MedicalConditions::findOrFail($id);
    $condition->update([
        'category_id' => $request->category_id
    ]);

    return back()->with('success', 'Category updated successfully!');
}
// Para sa Export Hub Landing Page
public function exportHub() 
{
    return view('admin.reports.export-reports'); 
}

// Para sa Universal Printing System
public function printReport(Request $request)
{
    $type = $request->query('type'); // mar, inventory, or appointment
    $output = trim((string) $request->query('output', 'html'));
    $monthFilter = $request->input('month', date('Y-m'));
    $year = date('Y', strtotime($monthFilter));
    $month = date('m', strtotime($monthFilter));

    $title = "";
    $data = [];

    if ($type == 'mar') {
        $title = "MONTHLY ACCOMPLISHMENT REPORT";
        // for categories
        $data = \App\Models\Category::with(['medicalConditions.consultations' => function($query) use ($year, $month) {
            $query->whereYear('consultation_date', $year)
                  ->whereMonth('consultation_date', $month);
        }])->get();
    } 
    elseif ($type == 'inventory') {
        $title = "INVENTORY STOCK REPORT";
        $data = $this->buildInventoryReportData($monthFilter);
    }
    elseif ($type == 'appointment') {
    $title = "APPOINTMENT SUMMARY REPORT";
    // for date
    $data = \App\Models\Appointment::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();
    }
    elseif ($type == 'health_forms') {
        $title = "ISSUED HEALTH FORMS REPORT";
        $monthStart = Carbon::parse($monthFilter . '-01')->startOfMonth();
        $monthEnd = (clone $monthStart)->endOfMonth();

        $data = HealthProfile::query()
            ->with('user')
            ->where('clearance_status', 'Issued')
            ->where(function ($builder) use ($monthStart, $monthEnd) {
                $builder->whereBetween('verified_at', [$monthStart, $monthEnd])
                    ->orWhere(function ($fallback) use ($monthStart, $monthEnd) {
                        $fallback->whereNull('verified_at')
                            ->whereBetween('created_at', [$monthStart, $monthEnd]);
                    });
            })
            ->get()
            ->groupBy(function (HealthProfile $form) {
                $course = trim((string) ($form->course_college ?: optional($form->user)->course ?: 'Unspecified Course'));
                return $course !== '' ? $course : 'Unspecified Course';
            })
            ->map(function ($forms, $course) {
                $sortedForms = $forms->sortByDesc(function (HealthProfile $form) {
                    return $form->verified_at ?? $form->created_at;
                })->values();

                $withConditionCount = $forms->where('has_illness', 'Yes')->count();
                $issuedCount = $forms->count();

                return (object) [
                    'course' => $course,
                    'issued_count' => $issuedCount,
                    'with_condition_count' => $withConditionCount,
                    'no_condition_count' => $issuedCount - $withConditionCount,
                    'last_issued_at' => optional($sortedForms->first())->verified_at ?? optional($sortedForms->first())->created_at,
                ];
            })
            ->sortByDesc('issued_count')
            ->values();
    }

    if ($output === 'pdf') {
        $pdf = Pdf::loadView('admin.reports.print-reports', [
            'data' => $data,
            'type' => $type,
            'title' => $title,
            'monthFilter' => $monthFilter,
            'isPdf' => true,
        ])->setPaper('a4', 'portrait');

        $fileType = $type !== '' ? $type : 'report';
        $safeMonth = preg_replace('/[^0-9\-]/', '', $monthFilter) ?: now()->format('Y-m');

        return $pdf->stream("{$fileType}-report-{$safeMonth}.pdf");
    }

    return view('admin.reports.print-reports', [
        'data' => $data,
        'type' => $type,
        'title' => $title,
        'monthFilter' => $monthFilter,
        'isPdf' => false,
    ]);
}
}
