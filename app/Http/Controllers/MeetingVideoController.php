<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingVideo;
use App\Services\BunnyStreamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MeetingVideoController extends Controller
{
    protected BunnyStreamService $bunny;

    public function __construct(BunnyStreamService $bunny)
    {
        $this->bunny = $bunny;
    }

    /**
     * Upload video to Bunny
     */
    public function store(Request $request, Meeting $meeting)
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4,mov,avi|max:512000', // 500MB
        ]);

        // Create video on Bunny
        $bunnyVideo = $this->bunny->createVideo($meeting->title);

        if (!isset($bunnyVideo['guid'])) {
            toast('error', 'Gagal membuat video di Bunny');
            return back();
        }

        $videoId = $bunnyVideo['guid'];

        // Simpan sementara
        $path = $request->file('video')->store('temp-videos');

        // Upload ke Bunny
        $this->bunny->uploadVideo($videoId, storage_path("app/{$path}"));

        // Hapus file lokal
        Storage::delete($path);

        // Simpan ke DB
        MeetingVideo::updateOrCreate(
            ['meeting_id' => $meeting->id],
            [
                'bunny_video_id' => $videoId,
                'playback_url'   => $this->bunny->playbackUrl($videoId),
                'library_id'     => config('bunny.library_id'),
                'status'         => 'uploading',
            ]
        );

        toast('success', 'Video berhasil diupload');
        return back();
    }

    /**
     * Delete video from Bunny
     */
    public function destroy(Meeting $meeting)
    {
        $video = $meeting->video;

        if (!$video) {
            return back()->with('error', 'Video tidak ditemukan');
        }

        $this->bunny->deleteVideo($video->bunny_video_id);

        $video->update([
            'status' => 'deleted',
        ]);

        toast('info', 'Video berhasil dihapus');
        return back();
    }
}
