<div wire:ignore>
    <canvas id="{{ $id ?? 'barChart' }}"></canvas>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('{{ $id ?? "barChart" }}').getContext('2d');
        
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
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    title: {
                        display: !! '{{ $title ?? "" }}',
                        text: '{{ $title ?? "" }}',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
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
                                size: 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
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
