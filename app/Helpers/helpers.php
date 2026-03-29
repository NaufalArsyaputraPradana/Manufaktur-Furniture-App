<?php

if (!function_exists('formatRupiah')) {
    /**
     * Format number to Rupiah currency
     */
    function formatRupiah($amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('formatNumber')) {
    /**
     * Format number with thousand separator
     */
    function formatNumber($number, int $decimals = 2): string
    {
        return number_format($number, $decimals, ',', '.');
    }
}

if (!function_exists('statusBadge')) {
    /**
     * Generate bootstrap badge HTML for status
     */
    function statusBadge(string $status, ?string $label = null): string
    {
        $colors = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'in_production' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            'cutting' => 'info',
            'assembly' => 'info',
            'sanding' => 'info',
            'finishing' => 'info',
            'quality_control' => 'warning',
            'paid' => 'success',
            'unpaid' => 'warning',
            'dp_paid' => 'info',
            'active' => 'success',
            'inactive' => 'secondary',
        ];

        $color = $colors[$status] ?? 'secondary';
        $text = $label ?? ucfirst(str_replace('_', ' ', $status));

        return "<span class='badge bg-{$color}'>{$text}</span>";
    }
}

if (!function_exists('progressBar')) {
    /**
     * Generate bootstrap progress bar HTML
     */
    function progressBar(int $percentage, string $color = 'primary'): string
    {
        return "
            <div class='progress' style='height: 25px;'>
                <div class='progress-bar bg-{$color}' role='progressbar' 
                     style='width: {$percentage}%' 
                     aria-valuenow='{$percentage}' 
                     aria-valuemin='0' 
                     aria-valuemax='100'>
                    {$percentage}%
                </div>
            </div>
        ";
    }
}

if (!function_exists('productionStageLabel')) {
    /**
     * Get Indonesian label for production stage
     */
    function productionStageLabel(string $stage): string
    {
        $labels = [
            'pending' => 'Menunggu',
            'cutting' => 'Pemotongan',
            'assembly' => 'Perakitan',
            'sanding' => 'Penghalusan',
            'finishing' => 'Finishing',
            'quality_control' => 'Quality Control',
            'completed' => 'Selesai',
        ];

        return $labels[$stage] ?? ucfirst($stage);
    }
}

if (!function_exists('orderStatusLabel')) {
    /**
     * Get Indonesian label for order status
     */
    function orderStatusLabel(string $status): string
    {
        $labels = [
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'in_production' => 'Dalam Produksi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return $labels[$status] ?? ucfirst($status);
    }
}

if (!function_exists('dateIndo')) {
    /**
     * Format date to Indonesian format
     */
    function dateIndo($date, $format = 'd M Y'): string
    {
        if (!$date) return '-';
        
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agt',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $carbonDate = \Carbon\Carbon::parse($date);
        
        if ($format === 'd M Y') {
            return $carbonDate->format('d') . ' ' . 
                   $months[(int)$carbonDate->format('m')] . ' ' . 
                   $carbonDate->format('Y');
        }
        
        return $carbonDate->format($format);
    }
}

if (!function_exists('timeAgo')) {
    /**
     * Get human readable time difference
     */
    function timeAgo($date): string
    {
        if (!$date) return '-';
        return \Carbon\Carbon::parse($date)->diffForHumans();
    }
}

if (!function_exists('currentUserId')) {
    /**
     * Get current authenticated user ID safely
     * Returns null if not authenticated
     *
     * @return int|null
     */
    function currentUserId(): ?int
    {
        return \Illuminate\Support\Facades\Auth::check() 
            ? (int) \Illuminate\Support\Facades\Auth::id() 
            : null;
    }
}

if (!function_exists('currentUser')) {
    /**
     * Get current authenticated user safely
     *
     * @return \App\Models\User|null
     */
    function currentUser(): ?\App\Models\User
    {
        /** @var \App\Models\User|null $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user;
    }
}

if (!function_exists('hasRole')) {
    /**
     * Check if current user has specific role
     *
     * @param string $roleName
     * @return bool
     */
    function hasRole(string $roleName): bool
    {
        $user = currentUser();
        return $user && $user->role && $user->role->name === $roleName;
    }
}

if (!function_exists('generateCode')) {
    /**
     * Generate unique code with prefix
     * Format: PREFIX-YYYYMMDD-XXXXX
     */
    function generateCode(string $prefix, ?string $modelClass = null, string $column = 'code'): string
    {
        $date = now()->format('Ymd');
        
        if ($modelClass) {
            $lastRecord = $modelClass::whereDate('created_at', today())
                ->orderBy('id', 'desc')
                ->first();
            
            $sequence = $lastRecord ? (int) substr($lastRecord->{$column}, -5) + 1 : 1;
        } else {
            $sequence = rand(1, 99999);
        }
        
        return sprintf('%s-%s-%05d', $prefix, $date, $sequence);
    }
}

if (!function_exists('sanitizeFileName')) {
    /**
     * Sanitize filename for safe storage
     */
    function sanitizeFileName(string $filename): string
    {
        // Remove special characters and spaces
        $filename = preg_replace('/[^A-Za-z0-9\-\_\.]/', '_', $filename);
        // Remove multiple underscores
        $filename = preg_replace('/_+/', '_', $filename);
        return strtolower($filename);
    }
}

if (!function_exists('truncateText')) {
    /**
     * Truncate text to specified length
     */
    function truncateText(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        
        return mb_substr($text, 0, $length) . $suffix;
    }
}

if (!function_exists('activeRoute')) {
    /**
     * Check if current route matches pattern
     * Returns 'active' class for navigation
     */
    function activeRoute(string|array $routes, string $class = 'active'): string
    {
        $routes = is_array($routes) ? $routes : [$routes];
        
        foreach ($routes as $route) {
            if (request()->routeIs($route . '*')) {
                return $class;
            }
        }
        
        return '';
    }
}

if (!function_exists('formatFileSize')) {
    /**
     * Format file size in human readable format
     */
    function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

if (!function_exists('calculatePercentage')) {
    /**
     * Calculate percentage safely (avoid division by zero)
     */
    function calculatePercentage(float $value, float $total, int $decimals = 2): float
    {
        if ($total == 0) {
            return 0;
        }
        
        return round(($value / $total) * 100, $decimals);
    }
}

if (!function_exists('stockStatusClass')) {
    /**
     * Get Bootstrap class for stock status
     */
    function stockStatusClass(float $stock, float $reorderPoint): string
    {
        if ($stock <= 0) {
            return 'danger';
        }
        
        if ($stock <= $reorderPoint) {
            return 'warning';
        }
        
        if ($stock <= $reorderPoint * 1.5) {
            return 'info';
        }
        
        return 'success';
    }
}
