<?php

namespace App\Accessories;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class OpenAIResponses
{
    private $apiKey;
    private $baseUrl = 'https://api.openai.com/v1';
    private $headers = [];
    
    public function __construct() {
        $this->apiKey = $this->getApiKey();
        $this->headers = [
            'Content-Type' => 'application/json',   
            'Authorization' => 'Bearer ' . $this->apiKey,
        ];
    }
    private function getApiKey(): string {
        return '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';
    }

    public function createConversation($items = [], $topic = "TheMoodDoctor") {
        $url = $this->baseUrl . '/conversations';
        $headers = $this->headers;
        $data = [
            "metadata" => [
                "topic" => $topic,
            ],
            "items" => $items,
        ];
        $conversation = Http::withHeaders($headers)->post($url, $data);
        if ($conversation->successful()) {
            $conversationData = $conversation->json();
            return $conversationData['id'];
        } else {
            Log::error('Failed to create conversation: ' . $conversation->body());
            return false;
        }
    }

    public function createResponse($conversation_id,$item,$model) {
    
        $url = $this->baseUrl . '/responses';
        $headers = $this->headers;

        $data = [
            "conversation" => $conversation_id,
            "model" => $model ?? "gpt-4o",
            "input" => $item,
        ];


        $response = Http::withHeaders($headers)->post($url, $data);
        if ($response->successful()) {
            return $response->json();
        }
    }
        
}

