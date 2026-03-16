@extends('layouts.admin')

@section('title', 'Edit Pesanan')

@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1">Edit Pesanan #{{ $order->order_number }}</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Pesanan</a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-arrow-left me-1"></i>Batal
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
                <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Menyimpan!</h6>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <form action="{{ route('admin.orders.update', $order) }}" method="POST" id="editOrderForm">
                    @csrf
                    @method('PUT')

                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Informasi Utama
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="user_id" class="form-label fw-bold">Pelanggan <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id"
                                        name="user_id" required>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ old('user_id', $order->user_id) == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} ({{ $customer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="order_date" class="form-label fw-bold">Tanggal Order <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('order_date') is-invalid @enderror"
                                        id="order_date" name="order_date"
                                        value="{{ old('order_date', $order->order_date ? $order->order_date->format('Y-m-d') : '') }}"
                                        required>
                                    @error('order_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="estimated_delivery_date" class="form-label fw-bold">Estimasi Selesai</label>
                                <input type="date"
                                    class="form-control @error('estimated_delivery_date') is-invalid @enderror"
                                    id="estimated_delivery_date" name="estimated_delivery_date"
                                    value="{{ old('estimated_delivery_date', $order->expected_completion_date ? $order->expected_completion_date->format('Y-m-d') : '') }}">
                                <div class="form-text">Tanggal perkiraan pesanan siap kirim.</div>
                                @error('estimated_delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="shipping_address" class="form-label fw-bold">Alamat Pengiriman <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control @error('shipping_address') is-invalid @enderror" id="shipping_address"
                                    name="shipping_address" rows="3" required>{{ old('shipping_address', $order->shipping_address) }}</textarea>
                                @error('shipping_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="form-label fw-bold">Catatan Pelanggan</label>
                                <textarea class="form-control @error('customer_notes') is-invalid @enderror" id="notes" name="notes"
                                    rows="3" placeholder="Informasi tambahan dari pelanggan...">{{ old('notes', $order->customer_notes) }}</textarea>
                                @error('customer_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2 border-top pt-4">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-light px-4">Batal</a>
                                <button type="submit" class="btn btn-primary px-4 shadow-sm" id="submitBtn">
                                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 bg-info bg-opacity-10 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold text-info"><i class="bi bi-info-circle-fill me-2"></i>Panduan Edit</h6>
                        <p class="mb-0 small text-dark">
                            Halaman ini hanya untuk mengubah informasi <strong>header</strong> pesanan (Pelanggan, Tanggal,
                            Alamat).
                        </p>
                        <hr class="my-3 opacity-10">
                        <p class="mb-0 small text-dark">
                            Untuk mengubah <strong>item produk</strong>, <strong>harga per item</strong>, atau
                            <strong>status produksi</strong>, silakan gunakan fitur di halaman
                            <a href="{{ route('admin.orders.show', $order) }}" class="fw-bold text-info">Detail
                                Pesanan</a>.
                        </p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <div class="small text-muted mb-1">Dibuat pada:</div>
                        <div class="small fw-bold mb-2">{{ $order->created_at->format('d M Y, H:i') }}</div>
                        <div class="small text-muted mb-1">Terakhir diupdate:</div>
                        <div class="small fw-bold">{{ $order->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('editOrderForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
        });
    </script>
@endpush
