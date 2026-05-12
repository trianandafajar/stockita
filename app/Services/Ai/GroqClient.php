<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GroqClient
{
    public function __construct(
        private readonly ?string $apiKey  = null,
        private readonly ?string $baseUrl = null,
        private readonly ?string $model   = null,
        private readonly ?int    $timeout = null,
    ) {}

    /**
     * Chat completion via Groq (OpenAI-compatible).
     *
     * @param  string  $system  System prompt — role/persona/format rules.
     * @param  string  $user    User prompt — data digest + concrete request.
     * @param  array   $opts    temperature, max_tokens, json (bool)
     * @return string  Assistant message content.
     */
    public function chat(string $system, string $user, array $opts = []): string
    {
        $key      = $this->apiKey  ?? config('services.groq.api_key');
        $baseUrl  = $this->baseUrl ?? config('services.groq.base_url');
        $model    = $this->model   ?? config('services.groq.model');
        $timeout  = $this->timeout ?? config('services.groq.timeout', 30);

        if (empty($key)) {
            throw new RuntimeException(
                'GROQ_API_KEY is not configured. Please add it to your .env file.'
            );
        }

        $payload = [
            'model'       => $model,
            'temperature' => $opts['temperature'] ?? 0.4,
            'max_tokens'  => $opts['max_tokens']  ?? 900,
            'messages'    => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user',   'content' => $user],
            ],
        ];

        if (!empty($opts['json'])) {
            $payload['response_format'] = [
                'type' => 'json_object',
            ];
        }

        $response = Http::withToken($key)
            ->acceptJson()
            ->timeout($timeout)
            ->post(rtrim($baseUrl, '/') . '/chat/completions', $payload);

        if (!$response->successful()) {
            Log::warning('[Groq] API call failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new RuntimeException(
                'Groq API request failed (HTTP ' . $response->status() . '). Check logs for details.'
            );
        }

        $content = $response->json('choices.0.message.content');

        if (!is_string($content) || trim($content) === '') {
            throw new RuntimeException(
                'Groq response is empty or invalid.'
            );
        }

        return trim($content);
    }
}
