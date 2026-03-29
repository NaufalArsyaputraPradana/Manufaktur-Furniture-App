<div wire:ignore>
    <canvas id="{{ $id ?? 'lineChart' }}"></canvas>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('{{ $id ?? "lineChart" }}').getContext('2d');
        
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
                    borderWidth: 2,
                    pointBackgroundColor: '{{ $dataset['pointColor'] ?? '#3b82f6' }}',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
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
