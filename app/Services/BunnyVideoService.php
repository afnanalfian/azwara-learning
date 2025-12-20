<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class BunnyVideoService
{
    public static function upload(UploadedFile $file, string $title): array
    {
        $libraryId = config('services.bunny.library_id');

        $response = Http::withHeaders([
            'AccessKey' => config('services.bunny.api_key'),
        ])->attach(
            'file',
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName()
        )->post(
            "https://video.bunnycdn.com/library/{$libraryId}/videos",
            ['title' => $title]
        );

        if (! $response->successful()) {
            throw new \RuntimeException($response->body());
        }

        return $response->json();
    }

    public static function delete(string $videoId): void
    {
        Http::withHeaders([
            'AccessKey' => config('services.bunny.api_key'),
        ])->delete(
            "https://video.bunnycdn.com/library/"
            . config('services.bunny.library_id')
            . "/videos/{$videoId}"
        )->throw();
    }

    public static function embedUrl(
        string $libraryId,
        string $videoId,
        int $userId
    ): string {
        $expires = now()->addMinutes(60)->timestamp;

        $token = hash_hmac(
            'sha256',
            $videoId . $expires . $userId,
            config('services.bunny.embed_secret')
        );

        return "https://iframe.mediadelivery.net/embed/{$libraryId}/{$videoId}"
             . "?token={$token}&expires={$expires}&userId={$userId}";
    }

    public static function fetchVideoMeta(string $libraryId, string $videoId): array
    {
        $response = Http::withHeaders([
            'AccessKey' => config('services.bunny.api_key'),
        ])->get(
            "https://video.bunnycdn.com/library/{$libraryId}/videos/{$videoId}"
        )->throw();

        return [
            'length'    => $response['length'] ?? null,
            'thumbnail' => $response['thumbnailFileName']
                ? "https://vz-{$libraryId}.b-cdn.net/{$videoId}/{$response['thumbnailFileName']}"
                : null,
        ];
    }
}
