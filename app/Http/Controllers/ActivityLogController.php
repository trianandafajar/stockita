<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');


        if (!auth()->user()->hasRole('admin')) {
            $query->where('store_id', auth()->user()->store->id);
        }

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->latest()->paginate(10);

        return view('logs.index', compact('logs'));
    }
}
