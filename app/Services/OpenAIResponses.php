<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIResponses
{
    private string $apiKey;
    private string $baseUrl = 'https://api.openai.com/v1';
    private array $headers = [];

    public function __construct()
    {
        $this->apiKey = $this->getApiKey();
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
        ];
    }

    private function getApiKey(): string
    {
        return (string) "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";
    }

    /**
     * Call the OpenAI Responses API with structured messages.
     *
     * @param array<int, array<string, mixed>> $messages
     */
    /**
     * Call Chat Completions with JSON response_format.
     *
     * @param array<int, array<string, string>> $messages
     */
    public function completeJson(array $messages, string $model = 'gpt-4o-mini'): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('OPENAI_API_KEY is not set.');
            return null;
        }

        $url = $this->baseUrl . '/chat/completions';
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'response_format' => ['type' => 'json_object'],
        ];

        $response = Http::withHeaders($this->headers)->post($url, $payload);

        if (!$response->successful()) {
            Log::error('OpenAI response error', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        }

        return $response->json();
    }
}