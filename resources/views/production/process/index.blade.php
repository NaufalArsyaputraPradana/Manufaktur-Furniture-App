@extends('layouts.production')

@section('title', 'Monitoring – Order #' . $order->order_number)

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Monitoring Proses Produksi</h4>
            <p class="text-muted mb-0 small">Order: <span class="fw-semibold">#{{ $order->order_number }}</span></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('production.monitoring.orders') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Daftar Order
            </a>
            <a href="{{ route('production.processes.create', ['order_id' => $order->id]) }}" class="btn btn-sm btn-success">
                <i class="bi bi-plus-circle me-1"></i>Tambah Proses
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-list-check text-primary me-2"></i>Daftar Tahapan</h5>
            <span class="badge bg-secondary">{{ $processes->count() }} tahap</span>
        </div>

        @if ($processes->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">#</th>
                            <th>Kode Produksi</th>
                            <th>Tahap</th>
                            <th>Status</th>
                            <th style="min-width:130px;">Progress</th>
                            <th>Petugas</th>
                            <th class="text-center">Dok.</th>
                            <th>Catatan</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($processes as $i => $process)
                            <tr>
                                <td class="ps-4 text-muted">{{ $i + 1 }}</td>
                                <td><span class="fw-bold">{{ $process->production_code }}</span></td>
                                <td><span class="badge bg-info bg-opacity-10 text-info">{{ $process->stage_label }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $process->status_color }}">{{ $process->status_label }}</span>
                                </td>
                                <td>
                                    <div class="progress mb-1" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $process->status_color }}"
                                            style="width: {{ $process->progress_percentage }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $process->progress_percentage }}%</small>
                                </td>
                                <td>
                                    @if ($process->assignedTo)
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-person-circle me-1"></i>{{ explode(' ', trim($process->assignedTo->name))[0] }}
                                        </span>
                                    @else
                                        <span class="text-muted fst-italic small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($process->documentation)
                                        <button type="button" class="btn btn-sm btn-outline-secondary documentation-preview-btn"
                                            data-img-url="{{ asset('storage/' . $process->documentation) }}"
                                            title="Lihat Dokumentasi">
                                            <i class="bi bi-file-earmark-image"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">–</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width:150px;" title="{{ $process->notes }}">
                                        {{ $process->notes ?? '–' }}
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        <a href="{{ route('production.processes.show', $process) }}"
                                            class="btn btn-sm btn-outline-primary bg-opacity-10 rounded-pill shadow-sm px-3"
                                            title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('production.processes.edit', $process) }}"
                                            class="btn btn-sm btn-outline-warning bg-opacity-10 rounded-pill shadow-sm px-3"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('production.processes.destroy', $process) }}"
                                            method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger bg-opacity-10 rounded-pill shadow-sm px-3"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-clipboard-x fs-1 text-muted opacity-50"></i>
                <p class="text-muted mt-2">Belum ada tahapan produksi untuk order ini.</p>
                <a href="{{ route('production.processes.create', ['order_id' => $order->id]) }}"
                    class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Tambah Proses Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!document.getElementById('docModal')) {
        document.body.insertAdjacentHTML('beforeend', `
            <div class="modal fade" id="docModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Preview Dokumentasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="" id="docModalImg" class="img-fluid rounded" style="max-width:100%;max-height:80vh;">
                        </div>
                    </div>
                </div>
            </div>`);
    }
    document.querySelectorAll('.documentation-preview-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('docModalImg').src = btn.getAttribute('data-img-url');
            new bootstrap.Modal(document.getElementById('docModal')).show();
        });
    });
});
</script>
@endpush
