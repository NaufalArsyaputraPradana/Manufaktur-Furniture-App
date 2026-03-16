<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GoogleTranslateService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://translation.googleapis.com/language/translate/v2';

    public function __construct()
    {
        $this->apiKey = config('services.google.translate_api_key', '');
    }

    /**
     * Translate text
     *
     * @param string $text
     * @param string $targetLanguage
     * @param string $sourceLanguage
     * @return string
     */
    public function translate(string $text, string $targetLanguage, string $sourceLanguage = 'auto'): string
    {
        // If API key not configured, return dummy translation
        if (empty($this->apiKey)) {
            return $this->dummyTranslate($text, $targetLanguage);
        }

        try {
            $cacheKey = "translate_{$sourceLanguage}_{$targetLanguage}_" . md5($text);

            return Cache::remember($cacheKey, 3600, function () use ($text, $targetLanguage, $sourceLanguage) {
                $response = Http::post($this->apiUrl, [
                    'key' => $this->apiKey,
                    'q' => $text,
                    'target' => $targetLanguage,
                    'source' => $sourceLanguage !== 'auto' ? $sourceLanguage : null,
                ]);

                if ($response->successful()) {
                    return $response->json('data.translations.0.translatedText', $text);
                }

                return $text;
            });
        } catch (\Exception $e) {
            return $text;
        }
    }

    /**
     * Translate multiple texts
     *
     * @param array $texts
     * @param string $targetLanguage
     * @param string $sourceLanguage
     * @return array
     */
    public function translateBatch(array $texts, string $targetLanguage, string $sourceLanguage = 'auto'): array
    {
        if (empty($this->apiKey)) {
            return array_map(fn($text) => $this->dummyTranslate($text, $targetLanguage), $texts);
        }

        try {
            $response = Http::post($this->apiUrl, [
                'key' => $this->apiKey,
                'q' => $texts,
                'target' => $targetLanguage,
                'source' => $sourceLanguage !== 'auto' ? $sourceLanguage : null,
            ]);

            if ($response->successful()) {
                return collect($response->json('data.translations', []))
                    ->pluck('translatedText')
                    ->toArray();
            }

            return $texts;
        } catch (\Exception $e) {
            return $texts;
        }
    }

    /**
     * Detect language of text
     *
     * @param string $text
     * @return string
     */
    public function detectLanguage(string $text): string
    {
        if (empty($this->apiKey)) {
            return 'id'; // Default Indonesian
        }

        try {
            $response = Http::post('https://translation.googleapis.com/language/translate/v2/detect', [
                'key' => $this->apiKey,
                'q' => $text,
            ]);

            if ($response->successful()) {
                return $response->json('data.detections.0.0.language', 'id');
            }

            return 'id';
        } catch (\Exception $e) {
            return 'id';
        }
    }

    /**
     * Get supported languages
     *
     * @return array
     */
    public function getSupportedLanguages(): array
    {
        return [
            'id' => 'Indonesian',
            'en' => 'English',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'ar' => 'Arabic',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'ru' => 'Russian',
        ];
    }

    /**
     * Dummy translation for simulation
     *
     * @param string $text
     * @param string $targetLanguage
     * @return string
     */
    protected function dummyTranslate(string $text, string $targetLanguage): string
    {
        $translations = [
            'en' => [
                'Kursi' => 'Chair',
                'Meja' => 'Table',
                'Lemari' => 'Cabinet',
                'Tempat Tidur' => 'Bed',
                'Rak' => 'Shelf',
                'Kayu' => 'Wood',
                'Besi' => 'Iron',
                'Kaca' => 'Glass',
            ],
            'zh' => [
                'Kursi' => '椅子',
                'Meja' => '桌子',
                'Lemari' => '柜子',
                'Tempat Tidur' => '床',
                'Rak' => '架子',
            ],
        ];

        return $translations[$targetLanguage][$text] ?? "[{$targetLanguage}] " . $text;
    }
}
