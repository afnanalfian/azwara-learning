<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BunnyStreamService
{
    protected string $apiKey;
    protected string $libraryId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey    = config('bunny.api_key');
        $this->libraryId = config('bunny.library_id');
        $this->baseUrl   = config('bunny.base_url');
    }

    /**
     * Create video
     */
    public function createVideo(string $title): array
    {
        $response = Http::withHeaders([
            'AccessKey' => $this->apiKey,
        ])->post("{$this->baseUrl}/library/{$this->libraryId}/videos", [
            'title' => $title,
        ]);

        return $response->json();
    }

    /**
     * Upload video file
     */
    public function uploadVideo(string $videoId, string $path)
    {
        return Http::withHeaders([
            'AccessKey' => $this->apiKey,
        ])->withBody(
            fopen($path, 'r'),
            'application/octet-stream'
        )->put("{$this->baseUrl}/library/{$this->libraryId}/videos/{$videoId}");
    }

    /**
     * Delete video
     */
    public function deleteVideo(string $videoId)
    {
        return Http::withHeaders([
            'AccessKey' => $this->apiKey,
        ])->delete("{$this->baseUrl}/library/{$this->libraryId}/videos/{$videoId}");
    }

    /**
     * Playback URL
     */
    public function playbackUrl(string $videoId): string
    {
        return "https://iframe.mediadelivery.net/embed/{$this->libraryId}/{$videoId}";
    }
}
