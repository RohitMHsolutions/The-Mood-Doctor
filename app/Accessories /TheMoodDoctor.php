<?php


class TheModdDoctor
{

    public function analyzeEmailEmotion(string $emailText): array
    {
        $OAResponses = new \App\Accessories\OpenAIResponses();

        $systemPrompt = <<<PROMPT
        You are an email emotion analysis assistant.

        Tasks:
        1. Analyze the emotional intensity of the email.
        2. Assign a rage score from 0 to 100.
        3. Rewrite the email into a calm, empathetic response.

        Rules:
        - Do NOT explain anything.
        - Do NOT add new facts.
        - Output ONLY valid JSON in this exact format:

        {
        "rage_score": number,
        "empathetic_response": "string"
        }
        PROMPT;

    $items = [[
        "type" => "message",
        "role" => "system",
        "content" => $systemPrompt
    ]];

    $conversationId = $OAResponses->createConversation($items, "email_emotion");

    if (!$conversationId) {
        return [
            'rage_score' => null,
            'empathetic_response' => null,
            'error' => 'Failed to create conversation'
        ];
    }

    $userMessage = [[
        "type" => "message",
        "role" => "user",
        "content" => $emailText
    ]];

    $model = "gpt-4o-mini";

    $aiResponse = $OAResponses->createResponse(
        $conversationId,
        $userMessage,
        tools: [],
        model: $model,
        response_format: [
            "type" => "json_object"
        ]
    );

    if (!$aiResponse || !isset($aiResponse['output'][0]['content'][0]['text'])) {
        return [
            'rage_score' => null,
            'empathetic_response' => null,
            'error' => 'Invalid AI response'
        ];
    }

    $rawText = $aiResponse['output'][0]['content'][0]['text'];
    $decoded = json_decode($rawText, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        Log::error('Invalid JSON from AI', ['response' => $rawText]);

        return [
            'rage_score' => null,
            'empathetic_response' => null,
            'error' => 'AI returned invalid JSON'
        ];
    }

    return [
        'rage_score' => (int) ($decoded['rage_score'] ?? 0),
        'empathetic_response' => $decoded['empathetic_response'] ?? ''
    ];
}
   

}
