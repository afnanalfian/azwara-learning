<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserEntitlementController extends Controller
{
    /**
     * INDEX
     * List seluruh hak akses siswa
     */
    public function index(Request $request)
    {
        // pastikan admin
        $this->authorizeAdmin();

        $q = $request->query('q');

        $users = User::query()
            ->whereHas('roles', fn ($q) => $q->where('name', 'siswa'))
            ->when($q, function ($qr) use ($q) {
                $qr->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->with([
                'entitlements' => function ($q) {
                    $q->orderBy('entitlement_type')
                      ->orderBy('entitlement_id');
                },
            ])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('entitlements.index', compact('users', 'q'));
    }

    /**
     * Guard khusus admin
     */
    protected function authorizeAdmin(): void
    {
        if (! auth()->check() || ! auth()->user()->hasRole('admin')) {
            abort(403);
        }
    }
}
