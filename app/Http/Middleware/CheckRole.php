<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  The allowed roles for this route
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this page.');
        }

        // Get user's role
        $user = auth()->user();
        $userRole = $user->role->name;

        // Check if user's role is in the allowed roles
        if (!in_array($userRole, $roles)) {
            // Log unauthorized access attempt
            Log::warning('Unauthorized access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $userRole,
                'required_roles' => $roles,
                'requested_url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Redirect user to their appropriate dashboard instead of showing 403
            return $this->redirectBasedOnRole($userRole, $request);
        }

        return $next($request);
    }

    /**
     * Redirect user to appropriate dashboard based on their role
     *
     * @param  string  $userRole
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectBasedOnRole(string $userRole, Request $request): Response
    {
        $message = 'You do not have permission to access this page. Redirected to your dashboard.';

        return match ($userRole) {
            'admin' => redirect()->route('admin.dashboard')
                ->with('error', $message),
            
            'production_staff' => redirect()->route('production.dashboard')
                ->with('error', $message),
            
            'customer' => redirect()->route('home')
                ->with('error', $message),
            
            default => redirect()->route('home')
                ->with('error', 'You do not have permission to access this page.'),
        };
    }
}
