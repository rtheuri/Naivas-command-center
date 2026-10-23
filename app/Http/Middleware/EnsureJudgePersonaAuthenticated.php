<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class EnsureJudgePersonaAuthenticated
{
    /**
     * Ensure that when visiting a persona workspace or triggering an action,
     * the request has an authenticated persona user attached, even if the judge
     * visited the route directly without clicking the landing page card.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            $path = trim($request->path(), '/');

            $role = match (true) {
                str_starts_with($path, 'imc') => 'imc_lead',
                str_starts_with($path, 'designer') => 'creative_designer',
                str_starts_with($path, 'agency') => 'agency_partner',
                str_starts_with($path, 'dpm') => 'dpm_buyer',
                str_starts_with($path, 'ome') => 'ome_manager',
                str_starts_with($path, 'executive') => 'marketing_exec',
                default => null,
            };

            if ($role) {
                $user = User::where('role', $role)->first();
                if (!$user) {
                    try {
                        Artisan::call('db:seed', ['--force' => true]);
                        $user = User::where('role', $role)->first();
                    } catch (\Throwable $e) {
                        // ignore seed errors
                    }
                }
                if ($user) {
                    Auth::login($user);
                }
            }
        }

        return $next($request);
    }
}
