<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$key = config('services.gemini.key');
$model = config('services.gemini.model');

echo "Key terbaca : " . ($key ? "YA (" . substr($key, 0, 6) . "...)" : "TIDAK (cek .env & config/services.php)") . PHP_EOL;
echo "Model       : " . $model . PHP_EOL;

if (!$key) {
    exit(1);
}

try {
    $res = Illuminate\Support\Facades\Http::withoutVerifying()
        ->timeout(15)
        ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}", [
            'contents' => [
                ['parts' => [['text' => 'Jawab singkat: Koneksi Gemini ke SIPADU berhasil!']]]
            ]
        ]);

    echo "HTTP Status : " . $res->status() . PHP_EOL;
    echo "Respon AI   : " . (data_get($res->json(), 'candidates.0.content.parts.0.text') ?? $res->body()) . PHP_EOL;
} catch (\Throwable $e) {
    echo "Error Koneksi: " . $e->getMessage() . PHP_EOL;
}