<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class GeminiClient
{
    public function configured(): bool
    {
        return trim((string) config('services.gemini.api_key')) !== '';
    }

    public function generateText(string $prompt, int $maxOutputTokens = 512, float $temperature = 0.2): ?string
    {
        return $this->generate([
            ['text' => $prompt],
        ], $maxOutputTokens, $temperature);
    }

    public function generateImageText(string $prompt, UploadedFile $file, int $maxOutputTokens = 4096, float $temperature = 0.0): ?string
    {
        $path = (string) $file->getRealPath();
        $bytes = @file_get_contents($path);
        if ($bytes === false) {
            return null;
        }

        $mimeType = $file->getMimeType() ?: 'image/jpeg';

        return $this->generate([
            [
                'inline_data' => [
                    'mime_type' => $mimeType,
                    'data' => base64_encode($bytes),
                ],
            ],
            ['text' => $prompt],
        ], $maxOutputTokens, $temperature);
    }

    private function generate(array $parts, int $maxOutputTokens, float $temperature): ?string
    {
        $apiKey = trim((string) config('services.gemini.api_key'));
        if ($apiKey === '') {
            return null;
        }

        $model = trim((string) config('services.gemini.model', ''));
        if ($model === '') {
            $model = 'gemini-3.5-flash';
        }

        try {
            $response = Http::withHeaders([
                'x-goog-api-key' => $apiKey,
            ])
                ->acceptJson()
                ->timeout(45)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'contents' => [[
                        'role' => 'user',
                        'parts' => $parts,
                    ]],
                    'generationConfig' => [
                        'temperature' => $temperature,
                        'maxOutputTokens' => $maxOutputTokens,
                    ],
                ]);

            if (!$response->successful()) {
                return null;
            }

            return $this->extractText($response->json() ?: []);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function extractText(array $payload): ?string
    {
        $parts = (array) data_get($payload, 'candidates.0.content.parts', []);
        $texts = [];

        foreach ($parts as $part) {
            $text = trim((string) data_get($part, 'text', ''));
            if ($text !== '') {
                $texts[] = $text;
            }
        }

        $content = trim(implode("\n", $texts));

        return $content !== '' ? $content : null;
    }
}
