<?php

namespace App\Http\Controllers\Course;

use App\Models\Meeting;
use App\Models\MeetingMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class MeetingMaterialController extends Controller
{
    /**
     * Store or replace meeting material (PDF)
     */
    public function store(Request $request, Meeting $meeting)
    {
        $request->validate([
            'material' => 'required|file|mimes:pdf|max:10240', // max 10MB
        ]);

        // Jika sudah ada materi sebelumnya, hapus dulu
        if ($meeting->material) {
            Storage::disk('public')->delete($meeting->material->file_path);
            $meeting->material->delete();
        }

        $file = $request->file('material');
        $path = $file->store('meeting-materials', 'public');

        MeetingMaterial::create([
            'meeting_id'    => $meeting->id,
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
        ]);

        /** NOTIFY STUDENTS */
        $users = $this->usersWithMeetingAccess($meeting);

        foreach ($users as $user) {
            notify_user(
                $user,
                "Materi meeting '{$meeting->title}' telah diupload.",
                false,
                route('meeting.show', $meeting)
            );
        }

        toast('success', 'Materi berhasil diupload');
        return back();
    }

    /**
     * Delete meeting material
     */
    public function destroy(Meeting $meeting)
    {
        if (!$meeting->material) {
            toast('warning', 'Materi tidak ditemukan');
            return back();
        }

        Storage::disk('public')->delete($meeting->material->file_path);
        $meeting->material->delete();

        toast('warning', 'Materi berhasil dihapus');
        return back();
    }
}
