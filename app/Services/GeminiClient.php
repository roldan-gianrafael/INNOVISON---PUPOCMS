<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class GeminiClient
{
    private ?string $lastError = null;

    public function configured(): bool
    {
        return trim((string) config('services.gemini.api_key')) !== '';
    }

    public function lastError(): ?string
    {
        return $this->lastError;
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
        $this->lastError = null;

        $apiKey = trim((string) config('services.gemini.api_key'));
        if ($apiKey === '') {
            $this->lastError = 'GEMINI_API_KEY is missing.';
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
                $message = trim((string) data_get($response->json() ?: [], 'error.message', ''));
                $this->lastError = 'Gemini API returned HTTP ' . $response->status() . ($message !== '' ? ': ' . $message : '.');
                return null;
            }

            $text = $this->extractText($response->json() ?: []);
            if ($text === null) {
                $this->lastError = 'Gemini API returned no text output.';
            }

            return $text;
        } catch (\Throwable $exception) {
            $this->lastError = 'Gemini request failed: ' . $exception->getMessage();
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
