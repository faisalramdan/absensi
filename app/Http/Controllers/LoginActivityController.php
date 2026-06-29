<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoginActivity;
use Jenssegers\Agent\Agent;
use Spatie\Permission\Models\user;


class LoginActivityController extends Controller
{
    public function index(Request $request)
    {
        $activities = LoginActivity::query();

        // Search email
        if ($request->filled('search')) {

            $search = $request->search;

            $activities->where('email', 'ILIKE', "%{$search}%");
        }

        $activities = $activities
            ->latest('logged_at')
            ->paginate(10)
            ->withQueryString();

        return view(
            'login-activities.index',
            compact('activities')
        );
    }
    public function show(LoginActivity $loginActivity)
    {
        $agent = new Agent();
        $agent->setUserAgent($loginActivity->user_agent);

        $browser = $agent->browser();
        $platform = $agent->platform();

        if ($agent->isDesktop()) {
            $device = 'Desktop';
        } elseif ($agent->isTablet()) {
            $device = 'Tablet';
        } elseif ($agent->isMobile()) {
            $device = 'Mobile';
        } else {
            $device = 'Unknown';
        }

        $relatedByIp = LoginActivity::where(
            'ip_address',
            $loginActivity->ip_address
        )
            ->where('id', '!=', $loginActivity->id)
            ->latest()
            ->take(5)
            ->get();

        $relatedByEmail = LoginActivity::where(
            'email',
            $loginActivity->email
        )
            ->where('id', '!=', $loginActivity->id)
            ->latest()
            ->take(5)
            ->get();

        return view(
            'login-activities.show',
            compact(
                'loginActivity',
                'relatedByIp',
                'relatedByEmail',
                'browser',
                'platform',
                'device'
            )
        );
    }
}
