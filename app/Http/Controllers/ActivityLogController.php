<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $logs = ActivityLog::with('user')
            ->when(!$user->hasRole('admin'), fn($q) =>
                $user->hasRole('buyer')
                ? $q->where('user_id', $user->id)
                : $q->where('store_id', $user->store->id)
            )
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()
            ->paginate(10);

        return view('logs.index', compact('logs'));
    }
}
