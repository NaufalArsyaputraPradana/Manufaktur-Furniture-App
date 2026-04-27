<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * MigrationController
 * 
 * Handles database migration operations for development/setup only.
 * 
 * Exposed routes:
 * - GET /migrate - Standard migration
 * - GET /migrate-fresh-seed - Complete database reset with seeding
 * 
 * ⚠️ WARNING: These are destructive operations. Secure with auth middleware!
 */
class MigrationController extends Controller
{
    /**
     * Run: php artisan migrate
     * 
     * Standard database migration without resetting existing data.
     * Safe to run on existing databases (only runs pending migrations).
     */
    public function migrate()
    {
        try {
            $output = new BufferedOutput();
            
            Artisan::call('migrate', [], $output);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Migration berhasil dijalankan',
                'output' => $output->fetch(),
                'timestamp' => now()->format('d-m-Y H:i:s'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Migration gagal: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run: php artisan migrate:fresh --seed
     * 
     * Complete database reset with fresh seeding.
     * 
     * ⚠️ DESTRUCTIVE: Drops all tables and re-creates them with seed data.
     * DEVELOPMENT ONLY - Do not use in production!
     * 
     * Returns JSON for API calls, redirects to home for web requests.
     */
    public function migrateFreshSeed(Request $request)
    {
        try {
            $output = new BufferedOutput();
            
            Artisan::call('migrate:fresh', ['--seed' => true], $output);
            
            // Return JSON for API/AJAX requests
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Database reset dan seed berhasil dijalankan',
                    'output' => $output->fetch(),
                    'timestamp' => now()->format('d-m-Y H:i:s'),
                ]);
            }
            
            // Redirect to home for regular web requests
            return redirect()->route('home')
                ->with('success', 'Database migration fresh dan seed berhasil dijalankan!');
            
        } catch (\Exception $e) {
            // Return JSON error for API/AJAX requests
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Migration fresh seed gagal: ' . $e->getMessage(),
                    'error' => $e->getMessage(),
                ], 500);
            }
            
            // Redirect with error for regular web requests
            return redirect()->route('home')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
