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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
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

        // Ensure roles are strings (convert any non-string values)
        $rolesArray = array_filter(array_map(function($role) {
            return is_string($role) ? trim($role) : null;
        }, $roles), function($role) {
            return $role !== null && $role !== '';
        });

        // Check if user's role is in the allowed roles
        if (!empty($rolesArray) && !in_array($userRole, $rolesArray)) {
            // Log unauthorized access attempt
            Log::warning('Unauthorized access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $userRole,
                'required_roles' => array_values($rolesArray),
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
