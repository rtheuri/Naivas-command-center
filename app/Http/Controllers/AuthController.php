<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display the Judge Portal landing page.
     */
    public function portal()
    {
        $users = User::all()->keyBy('role');

        return view('portal', [
            'users' => $users,
        ]);
    }

    /**
     * Instant frictionless login for judges based on role.
     */
    public function loginAs(string $role)
    {
        $validRoles = ['imc_lead', 'creative_designer', 'agency_partner', 'dpm_buyer', 'ome_manager', 'marketing_exec'];

        if (!in_array($role, $validRoles)) {
            return redirect()->route('portal')->with('error', 'Invalid role selection.');
        }

        $user = User::where('role', $role)->first();

        if (!$user) {
            // Re-seed if user doesn't exist
            Artisan::call('db:seed', ['--force' => true]);
            $user = User::where('role', $role)->first();
        }

        Auth::login($user);

        return match ($role) {
            'imc_lead' => redirect()->route('imc.dashboard')->with('success', 'Logged in as IMC Lead: ' . $user->name),
            'creative_designer' => redirect()->route('designer.dashboard')->with('success', 'Logged in as Creative Studio Lead: ' . $user->name),
            'agency_partner' => redirect()->route('agency.dashboard')->with('success', 'Logged in as External Agency Partner: ' . $user->name),
            'dpm_buyer' => redirect()->route('dpm.dashboard')->with('success', 'Logged in as DPM Media Lead: ' . $user->name),
            'ome_manager' => redirect()->route('ome.dashboard')->with('success', 'Logged in as OME Store Manager: ' . $user->name),
            'marketing_exec' => redirect()->route('executive.dashboard')->with('success', 'Logged in as Chief Marketing Officer: ' . $user->name),
            default => redirect()->route('executive.dashboard'),
        };
    }

    /**
     * Quick switcher to jump between personas while testing.
     */
    public function switchRole(string $role)
    {
        return $this->loginAs($role);
    }

    /**
     * Log out current user and return to Judge Portal.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal')->with('info', 'Logged out successfully. Select another judge persona to continue.');
    }

    /**
     * Reset mock database to pristine state for fresh demo.
     */
    public function resetDemo(Request $request)
    {
        // 1. Remember the current user's role and referring page before resetting database
        $currentRole = Auth::user()?->role;
        $previousUrl = url()->previous();

        // 2. In serverless environments (e.g. Vercel) where database is in /tmp,
        // copying the committed pristine database file is instantaneous and 100% resilient
        $srcDb = database_path('database.sqlite');
        $currentDb = config('database.connections.sqlite.database');

        if (file_exists($srcDb) && config('database.default') === 'sqlite' && ($currentDb === '/tmp/database.sqlite' || str_starts_with($currentDb, '/tmp/'))) {
            copy($srcDb, $currentDb);
        } else {
            // Standard migration & seed for local and persistent environments
            Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
        }

        // 3. Re-authenticate the active user in the newly created session
        if ($currentRole) {
            $user = User::where('role', $currentRole)->first();
            if ($user) {
                Auth::login($user);
            }
        }

        // 4. Return user back to their current page, or fallback to their role dashboard
        if ($previousUrl && !str_contains($previousUrl, 'demo/reset') && !str_contains($previousUrl, 'logout')) {
            return redirect($previousUrl)->with('success', 'Demo data has been successfully reset to baseline!');
        }

        if ($currentRole) {
            $targetRoute = match ($currentRole) {
                'imc_lead' => route('imc.dashboard'),
                'creative_designer' => route('designer.dashboard'),
                'agency_partner' => route('agency.dashboard'),
                'dpm_buyer' => route('dpm.dashboard'),
                'ome_manager' => route('ome.dashboard'),
                'marketing_exec' => route('executive.dashboard'),
                default => route('executive.dashboard'),
            };

            return redirect($targetRoute)->with('success', 'Demo data has been successfully reset to baseline!');
        }

        return redirect()->route('portal')->with('success', 'Demo data has been successfully reset to baseline!');
    }
}
