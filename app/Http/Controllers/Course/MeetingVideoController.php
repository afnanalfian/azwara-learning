<?php

namespace App\Http\Controllers\Course;

use App\Models\Meeting;
use App\Models\MeetingVideo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MeetingVideoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CREATE (form tambah video)
    |--------------------------------------------------------------------------
    */
    public function create(Meeting $meeting)
    {
        abort_if($meeting->video !== null, 409);

        return view('meetings.videos.create', compact('meeting'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE (simpan youtube video id)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request, Meeting $meeting)
    {
        abort_if(
            $meeting->video()->exists(),
            409,
            'Meeting sudah memiliki video'
        );

        $request->validate([
            'youtube_video_id' => 'required|string'
        ]);

        MeetingVideo::create([
            'meeting_id'        => $meeting->id,
            'title'             => $meeting->title,
            'youtube_video_id'  => $request->youtube_video_id,
        ]);

        /** NOTIFY STUDENTS WITH ACCESS */
        $users = $this->usersWithMeetingAccess($meeting);

        foreach ($users as $user) {
            notify_user(
                $user,
                "Video rekaman meeting '{$meeting->title}' sudah tersedia.",
                false,
                route('meeting.show', $meeting)
            );
        }

        toast('success', 'Video berhasil ditambahkan');
        return redirect()->route('meeting.show', $meeting);
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT (form edit video title + id)
    |--------------------------------------------------------------------------
    */
    public function edit(Meeting $meeting)
    {
        abort_if($meeting->video === null, 404);

        return view('meetings.videos.edit', [
            'meeting' => $meeting,
            'video'   => $meeting->video,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE (update metadata)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Meeting $meeting)
    {
        $video = $meeting->video;
        abort_if(! $video, 404);

        $request->validate([
            'title'             => 'required|string|max:255',
            'youtube_video_id'  => 'required|string',
        ]);

        $video->update([
            'title'             => $request->title,
            'youtube_video_id'  => $request->youtube_video_id,
        ]);
        toast('success', 'Metadata video berhasil diperbarui');
        return redirect()->route('meeting.show', $meeting);
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY (hapus video dari database)
    |--------------------------------------------------------------------------
    */
    public function destroy(Meeting $meeting)
    {
        $video = $meeting->video;
        abort_if(! $video, 404);

        $video->delete();
        toast('info', 'Video berhasil dihapus');
        return redirect()->route('meeting.show', $meeting);
    }

    /*
    |--------------------------------------------------------------------------
    | PLAYBACK (tampilkan youtube player)
    |--------------------------------------------------------------------------
    */
    public function playback(Meeting $meeting)
    {
        $video = $meeting->video;
        abort_if(! $video, 404);

        // generate embed url
        $embedUrl = "https://www.youtube.com/embed/{$video->youtube_video_id}?modestbranding=1&rel=0&showinfo=0";

        return view('meetings.videos.playback', compact(
            'meeting',
            'video',
            'embedUrl'
        ));
    }
}
