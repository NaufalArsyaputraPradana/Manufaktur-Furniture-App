<div wire:ignore class="position-relative w-100" style="min-height: 300px; overflow-x: auto;">
    <canvas id="{{ $id ?? 'barChart' }}" class="w-100"></canvas>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvasElement = document.getElementById('{{ $id ?? "barChart" }}');
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
                    backgroundColor: '{{ $dataset['backgroundColor'] ?? '#3b82f6' }}',
                    borderColor: '{{ $dataset['borderColor'] ?? '#1e40af' }}',
                    borderWidth: 1,
                    borderRadius: 4,
                    hoverBackgroundColor: '{{ $dataset['hoverColor'] ?? '#1e40af' }}'
                },
                @endforeach
            ]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                devicePixelRatio: window.devicePixelRatio || 1,
                indexAxis: isMobile ? 'y' : 'x', // Horizontal bars on mobile for better readability
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
                            }
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
