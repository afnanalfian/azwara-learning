<?php

namespace App\Http\Controllers;

use App\Models\MeetingVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BunnyWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Log dulu untuk debugging
        Log::info('Bunny Webhook Received', $request->all());

        $videoGuid = $request->input('VideoGuid');
        $status    = $request->input('Status');

        if (!$videoGuid) {
            return response()->json(['error' => 'No VideoGuid'], 400);
        }

        $video = MeetingVideo::where('bunny_video_id', $videoGuid)->first();

        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        match ($status) {
            'Finished' => $video->update(['status' => 'ready']),
            'Failed'   => $video->update(['status' => 'failed']),
            'Deleted'  => $video->update(['status' => 'deleted']),
            default    => null,
        };

        return response()->json(['ok' => true]);
    }
}
