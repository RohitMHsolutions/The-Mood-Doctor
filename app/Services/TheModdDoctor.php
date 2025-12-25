<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class TheModdDoctor
{
    // public function analyzeEmailEmotion(string $emailText): array
    // {
    //     $client = new OpenAIResponses();

    //     $systemPrompt = <<<PROMPT
    //         You are an email emotion analysis assistant.
    //         Tasks:
    //         1. Analyze the emotional intensity of the email.
    //         2. Assign a rage score from 0 to 100.
    //         3. Rewrite the email into a calm, empathetic response.
    //         Rules:
    //         - Do NOT explain anything.
    //         - Do NOT add new facts.
    //         - Output ONLY valid JSON in this exact format:
    //         {
    //         "rage_score": number,
    //         "empathetic_response": "string"
    //         }
    //         PROMPT;

    //     $messages = [
    //         ['role' => 'system', 'content' => $systemPrompt],
    //         ['role' => 'user', 'content' => $emailText],
    //     ];

    //     $response = $client->completeJson($messages, 'gpt-4o-mini');

    //     if (!$response || !isset($response['choices'][0]['message']['content'])) {
    //         return [
    //             'rage_score' => null,
    //             'empathetic_response' => null,
    //             'error' => 'Invalid AI response',
    //         ];
    //     }

    //     $rawText = $response['choices'][0]['message']['content'];
    //     $decoded = json_decode($rawText, true);

    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         Log::error('Invalid JSON from AI', ['response' => $rawText]);
    //         return [
    //             'rage_score' => null,
    //             'empathetic_response' => null,
    //             'error' => 'AI returned invalid JSON',
    //         ];
    //     }

    //     Log::info('AI rage analysis success', [
    //         'rage_score' => $decoded['rage_score'] ?? null,
    //         'has_reply' => isset($decoded['empathetic_response']),
    //     ]);

    //     return [
    //         'rage_score' => (int) ($decoded['rage_score'] ?? 0),
    //         'empathetic_response' => $decoded['empathetic_response'] ?? '',
    //     ];
    // }

    public function analyzeEmailEmotion(string $emailText): array
    {
        $client = new OpenAIResponses();

        $systemPrompt = <<<PROMPT
You are an advanced email emotion analysis assistant with multilingual awareness.

Tasks:
1. Detect the language of the email (e.g., English, Hindi, Gujarati, etc.).
2. Analyze the emotional intensity and emotional spectrum of the email.
3. Assign emotion scores from 0 to 100 for each emotion:
   - rage
   - sadness
   - frustration
   - anxiety
   - disappointment
   - joy
   - neutrality
4. Rewrite the email into a calm, empathetic response.
5. The rewritten response MUST be in the SAME language as the original email.

Rules:
- Do NOT explain anything.
- Do NOT add new facts.
- Preserve the original intent.
- Output ONLY valid JSON in this exact format:
{
  "language": "string",
  "emotions": {
    "rage": number,
    "sadness": number,
    "frustration": number,
    "anxiety": number,
    "disappointment": number,
    "joy": number,
    "neutrality": number
  },
  "empathetic_response": "string"
}
PROMPT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $emailText],
        ];

        $response = $client->completeJson($messages, 'gpt-4o-mini');

        if (!$response || !isset($response['choices'][0]['message']['content'])) {
            return [
                'language' => null,
                'emotions' => null,
                'empathetic_response' => null,
                'error' => 'Invalid AI response',
            ];
        }

        $rawText = $response['choices'][0]['message']['content'];
        $decoded = json_decode($rawText, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON from AI', ['response' => $rawText]);
            return [
                'language' => null,
                'emotions' => null,
                'empathetic_response' => null,
                'error' => 'AI returned invalid JSON',
            ];
        }

        Log::info('AI emotion analysis success', [
            'language' => $decoded['language'] ?? null,
            'emotions' => $decoded['emotions'] ?? null,
        ]);

        return [
            'language' => $decoded['language'] ?? '',
            'emotions' => $decoded['emotions'] ?? [],
            'empathetic_response' => $decoded['empathetic_response'] ?? '',
        ];
    }
}