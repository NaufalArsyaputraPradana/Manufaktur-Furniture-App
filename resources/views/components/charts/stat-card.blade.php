<div class="bg-white rounded border shadow-sm p-3 md:p-4 border-l-4 border-{{ $color ?? 'blue' }}-500">
    <div class="d-flex align-items-start justify-content-between">
        <div class="flex-grow-1">
            <!-- Title - Responsive font size -->
            <p class="text-muted small mb-2">{{ $title }}</p>
            <!-- Value - Mobile: h5, Desktop: h3 -->
            <h4 class="h5 md:h3 fw-bold text-dark mb-2">
                {{ $value }}
                @if ($unit)
                    <span class="text-muted fs-6">{{ $unit }}</span>
                @endif
            </h4>
            <!-- Trend Indicator - Mobile responsive -->
            @if ($trend)
                <div class="d-flex align-items-center {{ $trend > 0 ? 'text-success' : 'text-danger' }} small">
                    <i class="bi bi-{{ $trend > 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                    <span class="fw-semibold">
                        {{ abs($trend) }}% {{ $trend > 0 ? 'increase' : 'decrease' }}
                    </span>
                </div>
            @endif
        </div>
        <!-- Icon - Hidden on mobile, shown on larger screens -->
        @if ($icon)
            <div class="flex-shrink-0 p-2 md:p-3 bg-{{ $color ?? 'blue' }}-100 rounded d-none sm:block">
                <svg class="w-6 h-6 text-{{ $color ?? 'blue' }}-600" fill="currentColor" viewBox="0 0 20 20">
                    {{ $icon }}
                </svg>
            </div>
        @endif
    </div>
    <!-- Comparison - Mobile optimized -->
    @if ($comparison)
        <p class="mt-3 pt-3 border-top border-gray-200 text-muted small mb-0">
            {{ $comparison }}
        </p>
    @endif
</div>
