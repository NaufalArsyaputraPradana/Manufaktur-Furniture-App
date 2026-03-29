<div wire:ignore class="position-relative w-100" style="min-height: 250px;">
    <canvas id="{{ $id ?? 'pieChart' }}" class="w-100"></canvas>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvasElement = document.getElementById('{{ $id ?? "pieChart" }}');
        if (!canvasElement) return;
        
        const ctx = canvasElement.getContext('2d');
        
        // Detect mobile for responsive sizing
        const isMobile = window.innerWidth < 768;
        
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
                borderWidth: isMobile ? 1 : 2,
                hoverOffset: isMobile ? 8 : 10
            }]
        };

        const config = {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                devicePixelRatio: window.devicePixelRatio || 1,
                plugins: {
                    legend: {
                        display: true,
                        position: isMobile ? 'bottom' : 'bottom',
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
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: isMobile ? 8 : 12,
                        titleFont: {
                            size: isMobile ? 11 : 13,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: isMobile ? 10 : 12
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
