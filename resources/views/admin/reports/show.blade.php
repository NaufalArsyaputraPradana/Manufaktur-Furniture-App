@extends('layouts.app')

@section('title', 'Report: ' . $report->name ?? 'Report')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="bi bi-file-earmark-text"></i> {{ $report->name ?? 'Report' }}
                    </h1>
                    @if($report->description)
                        <p class="text-muted mb-0">{{ $report->description }}</p>
                    @endif
                </div>
                <div>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Information -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted small mb-1">Report Type</p>
                    <h6 class="mb-0">
                        <span class="badge bg-info">{{ ucfirst($report->type) }}</span>
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted small mb-1">Status</p>
                    <h6 class="mb-0">
                        {{ getStatusBadge($report->status) }}
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted small mb-1">Created By</p>
                    <h6 class="mb-0">{{ $report->user->name ?? 'Unknown' }}</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted small mb-1">Generated Date</p>
                    <h6 class="mb-0">{{ $report->generated_at ? $report->generated_at->format('d M Y H:i') : 'Not generated' }}</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group">
                        <button class="btn btn-primary" id="regenerateBtn">
                            <i class="bi bi-arrow-clockwise"></i> Regenerate Report
                        </button>
                        <div class="btn-group" role="group">
                            <button id="exportBtn" type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i> Export As
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.reports.export', [$report, 'format' => 'pdf']) }}">
                                    <i class="bi bi-file-pdf"></i> PDF
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.reports.export', [$report, 'format' => 'excel']) }}">
                                    <i class="bi bi-file-earmark-excel"></i> Excel
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.reports.export', [$report, 'format' => 'csv']) }}">
                                    <i class="bi bi-file-csv"></i> CSV
                                </a></li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.reports.edit', $report) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <button class="btn btn-danger" id="deleteBtn">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Content -->
    <div class="row">
        @if($report->data && is_array($report->data))
            <!-- If report has data, display charts/visualizations -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0">Report Data</h5>
                    </div>
                    <div class="card-body" id="reportDataContainer">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- If no data, show empty state -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                        <p class="text-muted mb-3">This report has no data yet.</p>
                        <button class="btn btn-primary" id="generateBtn">
                            <i class="bi bi-play-circle"></i> Generate Report
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Report Metadata -->
    @if($report->filters)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0">Filter Criteria</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            @foreach($report->filters as $key => $value)
                                <dt class="col-sm-3">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                                <dd class="col-sm-9">{{ is_array($value) ? json_encode($value) : $value }}</dd>
                            @endforeach
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const reportId = {{ $report->id }};
    const reportType = '{{ $report->type }}';

    document.getElementById('regenerateBtn')?.addEventListener('click', regenerateReport);
    document.getElementById('generateBtn')?.addEventListener('click', generateReport);
    document.getElementById('deleteBtn')?.addEventListener('click', deleteReport);

    async function generateReport() {
        try {
            const response = await fetch(`/api/reports/${reportId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    status: 'processing'
                })
            });

            if (response.ok) {
                // Simulate report generation
                showMessage('Report generation started...', 'info');
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        } catch (error) {
            console.error('Error generating report:', error);
            showMessage('Error generating report', 'danger');
        }
    }

    async function regenerateReport() {
        await generateReport();
    }

    async function deleteReport() {
        if (!confirm('Are you sure you want to delete this report?')) return;

        try {
            const response = await fetch(`/api/reports/${reportId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                showMessage('Report deleted successfully', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("admin.reports.index") }}';
                }, 1500);
            }
        } catch (error) {
            console.error('Error deleting report:', error);
            showMessage('Error deleting report', 'danger');
        }
    }

    function showMessage(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.row').firstChild);
    }
});
</script>
@endpush

@php
function getStatusBadge($status) {
    $badges = [
        'pending' => 'secondary',
        'processing' => 'info',
        'completed' => 'success',
        'failed' => 'danger'
    ];
    $color = $badges[$status] ?? 'secondary';
    return "<span class='badge bg-{$color}'>{$status}</span>";
}
@endphp
