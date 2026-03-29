<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $report->title }} - {{ now()->format('d M Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            background: #fff;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #4e73df;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 28px;
            color: #4e73df;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
            font-size: 14px;
        }
        
        .metadata {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }
        
        .metadata-item {
            padding: 10px;
        }
        
        .metadata-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .metadata-value {
            font-size: 14px;
            color: #333;
            margin-top: 5px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            color: #4e73df;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e3e6f0;
            font-weight: 600;
        }
        
        .stats {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: #fff;
            border: 1px solid #e3e6f0;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .stat-value {
            font-size: 24px;
            color: #4e73df;
            font-weight: 700;
            margin-top: 8px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 12px;
        }
        
        table thead {
            background: #4e73df;
            color: white;
        }
        
        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e3e6f0;
        }
        
        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .filters {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            font-size: 12px;
        }
        
        .filters strong {
            color: #4e73df;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e3e6f0;
            text-align: center;
            font-size: 11px;
            color: #999;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📊 {{ $report->title }}</h1>
            <p>Generated on {{ now()->format('d F Y \a\t H:i A') }}</p>
        </div>

        <!-- Metadata -->
        <div class="metadata">
            <div class="metadata-item">
                <div class="metadata-label">Report Type</div>
                <div class="metadata-value">
                    <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $report->report_type)) }}</span>
                </div>
            </div>
            <div class="metadata-item">
                <div class="metadata-label">Period</div>
                <div class="metadata-value">
                    {{ \Carbon\Carbon::parse($report->start_date)->format('d M Y') }} - 
                    {{ \Carbon\Carbon::parse($report->end_date)->format('d M Y') }}
                </div>
            </div>
            <div class="metadata-item">
                <div class="metadata-label">Status</div>
                <div class="metadata-value">
                    @if ($report->status === 'completed')
                        <span class="badge badge-success">{{ ucfirst($report->status) }}</span>
                    @elseif ($report->status === 'processing')
                        <span class="badge badge-warning">{{ ucfirst($report->status) }}</span>
                    @elseif ($report->status === 'failed')
                        <span class="badge badge-danger">{{ ucfirst($report->status) }}</span>
                    @else
                        <span class="badge badge-info">{{ ucfirst($report->status) }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Report Data Section -->
        <div class="section">
            <h2 class="section-title">Summary</h2>
            
            @if ($report->data)
                <div class="stats">
                    @foreach ($report->data as $key => $value)
                        <div class="stat-card">
                            <div class="stat-label">{{ str_replace('_', ' ', $key) }}</div>
                            <div class="stat-value">
                                @if (is_numeric($value))
                                    @if ($key === 'total_revenue' || str_contains($key, 'total'))
                                        Rp {{ number_format($value, 0, ',', '.') }}
                                    @else
                                        {{ number_format($value, 0) }}
                                    @endif
                                @else
                                    {{ $value }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($report->filters)
                <div class="filters">
                    <strong>Applied Filters:</strong><br>
                    @foreach ($report->filters as $key => $value)
                        @if ($value)
                            • {{ ucfirst(str_replace('_', ' ', $key)) }}: <strong>{{ $value }}</strong><br>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Additional Information -->
        <div class="section">
            <h2 class="section-title">Report Information</h2>
            <table>
                <tbody>
                    <tr>
                        <td><strong>Report ID</strong></td>
                        <td>#{{ $report->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Created By</strong></td>
                        <td>{{ $report->generatedBy?->name ?? 'System' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Created At</strong></td>
                        <td>{{ $report->created_at->format('d F Y H:i A') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Last Updated</strong></td>
                        <td>{{ $report->updated_at->format('d F Y H:i A') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This report was automatically generated by the Furniture Manufacturing System.</p>
            <p>For questions or discrepancies, please contact the system administrator.</p>
        </div>
    </div>
</body>
</html>
