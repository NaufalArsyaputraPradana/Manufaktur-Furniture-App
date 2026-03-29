<div wire:ignore class="position-relative" style="min-height: 250px; max-width: 100%;">
    <canvas id="{{ $id ?? 'lineChart' }}" class="w-100"></canvas>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvasElement = document.getElementById('{{ $id ?? "lineChart" }}');
        if (!canvasElement) return;
        
        const ctx = canvasElement.getContext('2d');
        
        // Detect mobile for responsive sizing
        const isMobile = window.innerWidth < 768;
        
        const data = {
            labels: @json($labels ?? []),
            datasets: [
                @foreach(($datasets ?? []) as $dataset)
                {
                    label: '{{ $dataset['label'] }}',
                    data: @json($dataset['data'] ?? []),
                    borderColor: '{{ $dataset['borderColor'] ?? '#3b82f6' }}',
                    backgroundColor: '{{ $dataset['backgroundColor'] ?? 'rgba(59, 130, 246, 0.1)' }}',
                    tension: 0.4,
                    borderWidth: isMobile ? 1.5 : 2,
                    pointBackgroundColor: '{{ $dataset['pointColor'] ?? '#3b82f6' }}',
                    pointBorderColor: '#fff',
                    pointBorderWidth: isMobile ? 1 : 2,
                    pointRadius: isMobile ? 3 : 4,
                    pointHoverRadius: isMobile ? 5 : 6,
                    fill: true,
                },
                @endforeach
            ]
        };

        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                devicePixelRatio: window.devicePixelRatio || 1,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: isMobile ? 10 : 20,
                            font: {
                                size: isMobile ? 10 : 12,
                                weight: 'bold'
                            }
                        }
                    },
                    title: {
                        display: !! '{{ $title ?? "" }}',
                        text: '{{ $title ?? "" }}',
                        font: {
                            size: isMobile ? 13 : 16,
                            weight: 'bold'
                        },
                        padding: isMobile ? 10 : 20
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: isMobile ? 9 : 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: isMobile ? 9 : 11
                            },
                            maxRotation: isMobile ? 45 : 0,
                            minRotation: isMobile ? 45 : 0
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        };

        new Chart(ctx, config);
    });
</script>
@endpush
