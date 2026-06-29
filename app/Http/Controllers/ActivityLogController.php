<?php

namespace App\Http\Controllers;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user');

        if ($request->filled('search')) {

            $search = $request->search;

            $logs->where(function ($q) use ($search) {

                $q->where('module', 'ILIKE', "%{$search}%")
                    ->orWhere('action', 'ILIKE', "%{$search}%")
                    ->orWhere('description', 'ILIKE', "%{$search}%");

            });
        }

        if ($request->filled('user_id')) {

            $logs->where('user_id', $request->user_id);

        }

        if ($request->filled('module')) {

            $logs->where('module', $request->module);

        }

        if ($request->filled('action')) {

            $logs->where('action', $request->action);

        }

        $logs = $logs
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $users = User::orderBy('name')->get();

        $modules = ActivityLog::select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        return view(
            'activity-logs.index',
            compact(
                'logs',
                'users',
                'modules'
            )
        );
    }
    public function show(ActivityLog $activityLog)
    {
        return view(
            'activity-logs.show',
            compact('activityLog')
        );
    }
}
