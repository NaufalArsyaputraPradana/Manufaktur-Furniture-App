@extends('layouts.production')

@section('title', 'Detail Proses - ' . $process->production_code)

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Detail Proses Produksi</h4>
            <p class="text-muted mb-0 small">Kode: <span class="fw-semibold">{{ $process->production_code }}</span></p>
        </div>
        <a href="{{ route('production.monitoring.orders') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    {{-- Progress Card --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4"
                        style="background: linear-gradient(135deg, #1a202c 0%, #4a5568 100%); color: white;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-3">
                                <div>
                                    <div class="small text-uppercase opacity-75 fw-bold">Progress Produksi</div>
                                    <div class="display-3 fw-bold">{{ $process->progress_percentage }}%</div>
                                </div>
                                <span
                                    class="badge bg-{{ $process->status_color }} fs-6 px-3 py-2">{{ $process->status_label }}</span>
                            </div>
                            <div class="progress" style="height: 10px; background-color: rgba(255,255,255,0.2);">
                                <div class="progress-bar bg-{{ $process->status_color }}"
                                    style="width: {{ $process->progress_percentage }}%;"></div>
                            </div>
                            @php
                                $stageKeys = array_filter(
                                    array_keys(\App\Models\ProductionProcess::STAGES),
                                    fn($k) => !in_array($k, ['pending', 'completed']),
                                );
                            @endphp
                            <div class="d-flex gap-1 mt-3">
                                @foreach ($stageKeys as $stageKey)
                                    <div class="flex-fill"
                                        style="height: 5px; border-radius: 5px; background: {{ (\App\Models\ProductionProcess::STAGES[$stageKey] ?? 0) <= $process->progress_percentage ? '#fff' : 'rgba(255,255,255,0.2)' }};"
                                        title="{{ \App\Models\ProductionProcess::STAGE_LABELS[$stageKey] ?? $stageKey }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Detail --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="fw-bold mb-0"><i class="bi bi-info-circle-fill text-primary me-2"></i>Informasi
                                Proses</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">Kode Produksi</small>
                                    <strong class="text-dark">{{ $process->production_code }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">Tahap</small>
                                    <span
                                        class="badge bg-info bg-opacity-10 text-info px-3 py-2">{{ $process->stage_label }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">Status</small>
                                    <span
                                        class="badge bg-{{ $process->status_color }} px-3 py-2">{{ $process->status_label }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">Progress</small>
                                    <strong>{{ $process->progress_percentage }}%</strong>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">Mulai Dikerjakan</small>
                                    <strong>{{ $process->started_at?->format('d M Y, H:i') ?? 'â€“' }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">Selesai</small>
                                    <strong>{{ $process->completed_at?->format('d M Y, H:i') ?? 'â€“' }}</strong>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Dibuat</small>
                                    <strong>{{ $process->created_at?->format('d M Y, H:i') ?? 'â€“' }}</strong>
                                </div>
                                @if ($process->notes)
                                    <div class="col-12">
                                        <small class="text-muted d-block">Catatan Lapangan</small>
                                        <div class="bg-light p-3 rounded-3 border">{{ $process->notes }}</div>
                                    </div>
                                @endif
                                @if ($process->documentation)
                                    <div class="col-12">
                                        <small class="text-muted d-block">Dokumentasi</small>
                                        @if (preg_match('/\.(jpg|jpeg|png)$/i', $process->documentation))
                                            <div class="d-inline-block text-center" style="max-width:120px;">
                                                <img src="{{ asset('storage/' . $process->documentation) }}" alt="Dokumentasi" class="img-thumbnail shadow-sm documentation-thumb" style="cursor:pointer; max-width:120px; max-height:120px;" data-src="{{ asset('storage/' . $process->documentation) }}" id="documentationThumbImg">
                                                <button type="button" class="btn btn-sm btn-outline-primary mt-2 documentation-view-btn" style="width:100%;" data-src="{{ asset('storage/' . $process->documentation) }}">
                                                    Lihat Gambar
                                                </button>
                                            </div>
                                            <!-- Modal for documentation preview -->
                                            <div class="modal fade" id="documentationModal" tabindex="-1" aria-labelledby="documentationModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="documentationModalLabel">Preview Dokumentasi</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <img id="documentationModalImg" src="" alt="Dokumentasi" class="img-fluid rounded shadow">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    var thumbImg = document.getElementById('documentationThumbImg');
                                                    var viewBtn = document.querySelector('.documentation-view-btn');
                                                    var modalImg = document.getElementById('documentationModalImg');
                                                    var modal = new bootstrap.Modal(document.getElementById('documentationModal'));
                                                    if (thumbImg) {
                                                        thumbImg.addEventListener('click', function() {
                                                            var src = this.getAttribute('data-src');
                                                            modalImg.src = src;
                                                            modal.show();
                                                        });
                                                    }
                                                    if (viewBtn) {
                                                        viewBtn.addEventListener('click', function() {
                                                            var src = this.getAttribute('data-src');
                                                            modalImg.src = src;
                                                            modal.show();
                                                        });
                                                    }
                                                });
                                            </script>
                                        @else
                                            <a href="{{ asset('storage/' . $process->documentation) }}" target="_blank" class="btn btn-outline-primary">
                                                <i class="bi bi-file-earmark-image me-2"></i>Lihat File
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Danger Zone --}}
                    <div class="card border-0 shadow-sm rounded-4 border-danger">
                        <div class="card-header bg-danger text-white border-0 py-3">
                            <h5 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Danger Zone</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <p class="mb-0 text-danger fw-medium">Menghapus proses ini bersifat permanen.</p>
                                <form action="{{ route('production.processes.destroy', $process) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus proses ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-2"></i>Hapus
                                        Proses</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Order & Petugas --}}
                <div class="col-lg-4">
                    {{-- Informasi Order --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="fw-bold mb-0"><i class="bi bi-box-seam-fill text-primary me-2"></i>Informasi Order
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <small class="text-muted d-block">No. Order</small>
                                <strong class="text-primary">#{{ $process->order?->order_number ?? 'â€“' }}</strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Customer</small>
                                <strong>{{ $process->order?->user?->name ?? 'â€“' }}</strong>
                            </div>
                            @if ($process->orderDetail?->product)
                                <div class="mb-3">
                                    <small class="text-muted d-block">Produk</small>
                                    <strong>{{ $process->orderDetail->product->name }}</strong>
                                </div>
                            @endif
                            <div>
                                <small class="text-muted d-block">Status Order</small>
                                <span
                                    class="badge bg-{{ $process->order?->status_color ?? 'secondary' }}">{{ $process->order?->status_label ?? ucfirst(str_replace('_', ' ', $process->order?->status ?? 'â€“')) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Petugas --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="fw-bold mb-0"><i class="bi bi-person-badge-fill text-primary me-2"></i>Petugas</h5>
                        </div>
                        <div class="card-body p-4">
                            @if ($process->assignedTo)
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px; font-size: 1.5rem; font-weight: bold;">
                                        {{ strtoupper(substr($process->assignedTo->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $process->assignedTo->name }}</div>
                                        <small class="text-muted">{{ $process->assignedTo->email }}</small>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted mb-0">Belum ada staff yang ditugaskan.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Riwayat Log --}}
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Log</h5>
                            <span class="badge bg-secondary">{{ $process->logs->count() }}</span>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse ($process->logs->take(10) as $log)
                                <div class="list-group-item p-3">
                                    <div class="d-flex gap-3">
                                        <div class="flex-shrink-0">
                                            <span
                                                class="badge bg-{{ $log->action_color ?? 'secondary' }} rounded-circle p-2">
                                                <i class="bi {{ $log->action_icon ?? 'bi-circle-fill' }} text-white"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <strong
                                                    class="small">{{ $log->action_label ?? ucfirst($log->action) }}</strong>
                                                <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                            </div>
                                            @if ($log->notes)
                                                <p class="small text-muted mb-1">{{ $log->notes }}</p>
                                            @endif
                                            <small class="text-muted"><i
                                                    class="bi bi-person-fill me-1"></i>{{ $log->user?->name ?? 'Sistem' }}</small>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center py-4">
                                    <i class="bi bi-journal-x fs-2 text-muted opacity-50"></i>
                                    <p class="text-muted mb-0">Belum ada log</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
</div>
@endsection

@push('styles')
    <style>
        .progress-bar {
            transition: width 0.8s ease;
        }
    </style>
@endpush
