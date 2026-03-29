<div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-{{ $color ?? 'blue' }}-500">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-gray-600 text-sm font-medium mb-2">{{ $title }}</p>
            <h3 class="text-3xl font-bold text-gray-900 mb-2">
                {{ $value }}
                @if ($unit)
                    <span class="text-lg text-gray-500">{{ $unit }}</span>
                @endif
            </h3>
            @if ($trend)
                <div class="flex items-center {{ $trend > 0 ? 'text-green-600' : 'text-red-600' }}">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        @if ($trend > 0)
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414-1.414L13.586 7H12z" clip-rule="evenodd" />
                        @else
                            <path fill-rule="evenodd" d="M12 13a1 1 0 110 2H7a1 1 0 01-1-1V9a1 1 0 112 0v2.586l4.293-4.293a1 1 0 011.414 1.414L9.414 13H12z" clip-rule="evenodd" />
                        @endif
                    </svg>
                    <span class="text-sm font-semibold">
                        {{ abs($trend) }}% {{ $trend > 0 ? 'increase' : 'decrease' }}
                    </span>
                </div>
            @endif
        </div>
        @if ($icon)
            <div class="flex-shrink-0 p-3 bg-{{ $color ?? 'blue' }}-100 rounded-full">
                <svg class="w-6 h-6 text-{{ $color ?? 'blue' }}-600" fill="currentColor" viewBox="0 0 20 20">
                    {{ $icon }}
                </svg>
            </div>
        @endif
    </div>
    @if ($comparison)
        <p class="mt-4 pt-4 border-t border-gray-200 text-xs text-gray-500">
            {{ $comparison }}
        </p>
    @endif
</div>
