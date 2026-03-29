<div wire:ignore>
    <canvas id="{{ $id ?? 'pieChart' }}"></canvas>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('{{ $id ?? "pieChart" }}').getContext('2d');
        
        const colors = [
            '#3b82f6', '#ef4444', '#10b981', '#f59e0b', 
            '#8b5cf6', '#ec4899', '#14b8a6', '#f97316',
            '#06b6d4', '#84cc16', '#6366f1', '#d946ef'
        ];

        const data = {
            labels: @json($labels ?? []),
            datasets: [{
                data: @json($data ?? []),
                backgroundColor: @json($colors->slice(0, count($labels ?? [])) ?? $colors),
                borderColor: '#fff',
                borderWidth: 2,
                hoverOffset: 10
            }]
        };

        const config = {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
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
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 13,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        borderColor: '#fff',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        };

        new Chart(ctx, config);
    });
</script>
@endpush
