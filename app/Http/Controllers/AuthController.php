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
            'creative_designer' => redirect()->route('designer.dashboard')->with('success', 'Logged in as Creative Designer: ' . $user->name),
            'agency_partner' => redirect()->route('agency.dashboard')->with('success', 'Logged in as External Agency Partner: ' . $user->name),
            'dpm_buyer' => redirect()->route('dpm.dashboard')->with('success', 'Logged in as DPM Media Buyer: ' . $user->name),
            'ome_manager' => redirect()->route('ome.dashboard')->with('success', 'Logged in as OME Store Manager: ' . $user->name),
            'marketing_exec' => redirect()->route('executive.dashboard')->with('success', 'Logged in as Marketing Executive: ' . $user->name),
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
    public function resetDemo()
    {
        Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);

        if (Auth::check()) {
            $currentRole = Auth::user()->role;
            $user = User::where('role', $currentRole)->first();
            if ($user) {
                Auth::login($user);
            }
        }

        return back()->with('success', 'Demo database has been reset and re-seeded with fresh mock data!');
    }
}
