@extends('layouts.admin')

@section('title', 'Kalkulator Harga')

@push('styles')
    <style>
        :root { --admin-primary: #4e73df; --admin-secondary: #224abe; }
        .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important; }
        .komponen-row td { padding: 0.5rem !important; }
        .cost-input-group .input-group-text { background-color: #f8f9fc; font-weight: 600; font-size: 0.8rem; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1">Kalkulator Harga Custom</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.custom-orders.index') }}" class="text-decoration-none">Order Custom</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Hitung BOM</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.custom-orders.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger mb-4 shadow-sm border-0 border-start border-4 border-danger" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Perhatian!</h6>
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4">
            <!-- Sidebar: Detail Permintaan -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>Detail Item</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted fw-bold text-uppercase d-block mb-1">Nama Produk</label>
                            <div class="fw-bold fs-5 text-dark">{{ $orderDetail->product_name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted fw-bold text-uppercase d-block mb-1">Pelanggan</label>
                            <div class="text-dark">{{ $orderDetail->order?->user?->name ?? '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted fw-bold text-uppercase d-block mb-1">Jumlah Pesanan</label>
                            <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle fs-6 px-3">{{ $orderDetail->quantity }} Unit</span>
                        </div>
                        <div class="mb-4">
                            <label class="small text-muted fw-bold text-uppercase d-block mb-1">Spesifikasi Custom</label>
                            <div class="bg-light p-3 rounded border text-secondary small" style="line-height: 1.6;">
                                {{ $orderDetail->custom_specifications['description'] ?? 'Tidak ada deskripsi detail.' }}
                            </div>
                        </div>
                        @if (!empty($orderDetail->custom_specifications['design_image']))
                            @php
                                $designPath = $orderDetail->custom_specifications['design_image'];
                                $designUrl = asset('storage/' . $designPath);
                                $isPdf = str_ends_with(strtolower($designPath), '.pdf');
                            @endphp
                            <div class="mb-2">
                                <label class="small text-muted fw-bold text-uppercase d-block mb-1">Desain referensi</label>
                                @if ($isPdf)
                                    <a href="{{ $designUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-danger btn-sm w-100 mb-2">
                                        <i class="bi bi-file-earmark-pdf me-1"></i>Buka PDF desain
                                    </a>
                                @else
                                    <button type="button" class="btn btn-link p-0 border-0 w-100 text-decoration-none design-thumb-trigger" data-src="{{ $designUrl }}" data-title="Desain custom — {{ $orderDetail->product_name }}">
                                        <img src="{{ $designUrl }}" alt="Desain" class="img-fluid rounded border shadow-sm w-100 design-thumb"
                                            style="max-height: 250px; object-fit: cover; cursor: zoom-in;">
                                    </button>
                                    <small class="text-muted mt-2 d-block text-center fst-italic">Klik untuk preview & zoom</small>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Area: Calculator -->
            <div class="col-lg-8">
                <form action="{{ route('admin.custom-orders.store', $orderDetail->id) }}" method="POST" id="calculatorForm">
                    @csrf

                    <!-- Section 1: Timber Components -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-success"><i class="bi bi-grid-3x3 me-2"></i>1. Perhitungan Kubikasi Kayu</h6>
                            <button type="button" class="btn btn-sm btn-success shadow-sm" onclick="addRow()">
                                <i class="bi bi-plus-lg me-1"></i>Tambah Komponen
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div class="row mb-4 align-items-end">
                                <div class="col-md-6">
                                    <label for="grade" class="form-label fw-bold small text-secondary">Grade Kayu & Harga per m³</label>
                                    <select name="grade" id="grade" class="form-select border-success-subtle" required onchange="calculateAll()">
                                        <option value="">-- Pilih Kualitas Kayu --</option>
                                        <option value="A" data-price="50000000" {{ ($existingBom['grade'] ?? '') == 'A' ? 'selected' : '' }}>Grade A (Premium - Rp 50jt/m³)</option>
                                        <option value="B" data-price="30000000" {{ ($existingBom['grade'] ?? '') == 'B' ? 'selected' : '' }}>Grade B (Standar - Rp 30jt/m³)</option>
                                        <option value="C" data-price="12000000" {{ ($existingBom['grade'] ?? '') == 'C' ? 'selected' : '' }}>Grade C (Ekonomis - Rp 12jt/m³)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-2 bg-success bg-opacity-10 rounded border border-success border-opacity-25 text-center">
                                        <label class="d-block small text-muted text-uppercase fw-bold mb-1">Total Biaya Material Kayu</label>
                                        <span class="fs-5 fw-bold text-success" id="total_wood_cost_display">Rp 0</span>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle" id="komponen-table">
                                    <thead class="bg-light text-center small text-uppercase fw-bold">
                                        <tr>
                                            <th width="30%">Nama Komponen</th>
                                            <th width="12%">P (cm)</th>
                                            <th width="12%">L (cm)</th>
                                            <th width="12%">T (cm)</th>
                                            <th width="10%">Qty</th>
                                            <th width="20%">Volume (m³)</th>
                                            <th width="4%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="komponen-body">
                                        <!-- JS Row Injection -->
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold small pe-3 py-2">Total Akumulasi Volume:</td>
                                            <td colspan="2" class="py-2"><span id="total_volume_display" class="fw-bold text-dark">0.000000 m³</span></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Other Production Costs -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-warning"><i class="bi bi-tools me-2"></i>2. Biaya Produksi & Bahan Pendukung</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 cost-input-group">
                                <div class="col-md-4">
                                    <label for="production_cost" class="form-label small fw-bold">Upah Tukang / Produksi</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" id="production_cost" name="production_cost" class="form-control cost-input"
                                            value="{{ old('production_cost', $existingBom['costs']['production'] ?? 0) }}" min="0" oninput="calculateAll()">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="rattan_cost" class="form-label small fw-bold">Bahan Rotan / Anyaman</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" id="rattan_cost" name="rattan_cost" class="form-control cost-input"
                                            value="{{ old('rattan_cost', $existingBom['costs']['rattan'] ?? 0) }}" min="0" oninput="calculateAll()">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="foam_cost" class="form-label small fw-bold">Bahan Busa / Jok</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" id="foam_cost" name="foam_cost" class="form-control cost-input"
                                            value="{{ old('foam_cost', $existingBom['costs']['foam'] ?? 0) }}" min="0" oninput="calculateAll()">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="finishing_cost" class="form-label small fw-bold">Biaya Finishing</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" id="finishing_cost" name="finishing_cost" class="form-control cost-input"
                                            value="{{ old('finishing_cost', $existingBom['costs']['finishing'] ?? 0) }}" min="0" oninput="calculateAll()">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="other_cost" class="form-label small fw-bold">Lain-lain (Hardware/Kaca)</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" id="other_cost" name="other_cost" class="form-control cost-input"
                                            value="{{ old('other_cost', $existingBom['costs']['other'] ?? 0) }}" min="0" oninput="calculateAll()">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-2 bg-warning bg-opacity-10 rounded border border-warning border-opacity-25 text-center">
                                        <small class="d-block text-muted text-uppercase fw-bold mb-1">Total HPP Produksi</small>
                                        <span class="fs-5 fw-bold text-dark" id="total_hpp_display">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Profit & Final Price -->
                    <div class="card border-0 shadow-lg rounded-3 mb-4 bg-primary bg-opacity-10 border-start border-4 border-primary">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-5">
                                    <label for="profit_percent" class="form-label fw-bold text-primary">Margin Keuntungan (%)</label>
                                    <div class="input-group shadow-sm">
                                        <input type="number" name="profit_percent" id="profit_percent" class="form-control fw-bold fs-5 py-2"
                                            value="{{ old('profit_percent', $existingBom['profit_percent'] ?? 25) }}" min="0" max="100" step="1" oninput="calculateAll()">
                                        <span class="input-group-text bg-white text-primary fw-bold px-3">%</span>
                                    </div>
                                    <small class="text-muted mt-2 d-block fst-italic">Rekomendasi industri: 20-35%</small>
                                </div>
                                <div class="col-md-7 text-end">
                                    <label class="form-label fw-bold text-primary d-block mb-1">Estimasi Harga Jual per Unit</label>
                                    <h2 class="mb-0 fw-bold text-primary text-shadow" id="final_price_display">Rp 0</h2>
                                    <div class="small text-muted mt-1">*Harga dibulatkan ke atas ke ribuan terdekat</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-3 justify-content-end mb-5">
                        <a href="{{ route('admin.custom-orders.index') }}" class="btn btn-lg btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-lg btn-primary shadow-sm px-5 fw-bold" id="submitBtn">
                            <i class="bi bi-save me-2"></i>Simpan Hasil Kalkulasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="designLightboxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-sm-down">
            <div class="modal-content bg-dark border-0">
                <div class="modal-header border-0 py-2 px-3 align-items-center">
                    <h6 class="modal-title text-white mb-0 text-truncate pe-2" id="designLightboxTitle">Preview</h6>
                    <div class="d-flex align-items-center gap-2 ms-auto">
                        <button type="button" class="btn btn-sm btn-outline-light" id="designZoomOut" title="Zoom out">−</button>
                        <button type="button" class="btn btn-sm btn-outline-light" id="designZoomIn" title="Zoom in">+</button>
                        <button type="button" class="btn btn-sm btn-outline-light" id="designZoomReset" title="Reset">100%</button>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                </div>
                <div class="modal-body p-0 d-flex align-items-center justify-content-center overflow-auto" id="designLightboxBody" style="min-height: 50vh; max-height: 85vh; cursor: grab;">
                    <img src="" alt="" id="designLightboxImg" class="rounded shadow" style="max-width: none; transition: transform 0.15s ease-out; transform-origin: center center;">
                </div>
                <div class="modal-footer border-0 bg-dark text-white-50 small py-2">
                    Scroll untuk zoom (jika didukung) atau gunakan tombol +/−
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let rowIndex = 0;
        const existingComponents = @json($existingBom['komponen'] ?? []);

        function formatRupiah(num) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(num);
        }

        function addRow(data = null) {
            const tbody = document.getElementById('komponen-body');
            const row = document.createElement('tr');
            row.className = 'komponen-row animate__animated animate__fadeIn';

            row.innerHTML = `
                <td><input type="text" name="komponen[${rowIndex}][nama]" class="form-control form-control-sm" required placeholder="Contoh: Kaki Depan" value="${data ? data.nama : ''}"></td>
                <td><input type="number" name="komponen[${rowIndex}][panjang]" class="form-control form-control-sm dim-input" required step="0.1" value="${data ? data.panjang : 0}" oninput="calculateRow(this)"></td>
                <td><input type="number" name="komponen[${rowIndex}][lebar]" class="form-control form-control-sm dim-input" required step="0.1" value="${data ? data.lebar : 0}" oninput="calculateRow(this)"></td>
                <td><input type="number" name="komponen[${rowIndex}][tebal]" class="form-control form-control-sm dim-input" required step="0.1" value="${data ? data.tebal : 0}" oninput="calculateRow(this)"></td>
                <td><input type="number" name="komponen[${rowIndex}][jumlah]" class="form-control form-control-sm dim-input text-center fw-bold" required min="1" value="${data ? data.jumlah : 1}" oninput="calculateRow(this)"></td>
                <td><input type="text" class="form-control form-control-sm bg-light volume-display text-end font-monospace" readonly value="0.000000"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(this)" title="Hapus Komponen"><i class="bi bi-trash"></i></button>
                </td>
            `;

            tbody.appendChild(row);
            if (data) {
                calculateRow(row.querySelector('.dim-input'));
            }
            rowIndex++;
        }

        function removeRow(btn) {
            btn.closest('tr').remove();
            calculateAll();
        }

        function calculateRow(element) {
            const row = element.closest('tr');
            const p = parseFloat(row.querySelector('input[name*="[panjang]"]').value) || 0;
            const l = parseFloat(row.querySelector('input[name*="[lebar]"]').value) || 0;
            const t = parseFloat(row.querySelector('input[name*="[tebal]"]').value) || 0;
            const qty = parseInt(row.querySelector('input[name*="[jumlah]"]').value) || 0;

            const vol = (p * l * t * qty) / 1000000;
            row.querySelector('.volume-display').value = vol.toFixed(6);
            calculateAll();
        }

        function calculateAll() {
            let totalVol = 0;
            document.querySelectorAll('.volume-display').forEach(el => { totalVol += parseFloat(el.value) || 0; });
            document.getElementById('total_volume_display').innerText = totalVol.toFixed(6) + ' m³';

            const gradeSelect = document.getElementById('grade');
            const pricePerM3 = parseFloat(gradeSelect.options[gradeSelect.selectedIndex]?.dataset.price) || 0;
            const woodCost = totalVol * pricePerM3;
            document.getElementById('total_wood_cost_display').innerText = formatRupiah(woodCost);

            let additionalCosts = 0;
            document.querySelectorAll('.cost-input').forEach(el => { additionalCosts += parseFloat(el.value) || 0; });

            const totalHpp = woodCost + additionalCosts;
            document.getElementById('total_hpp_display').innerText = formatRupiah(totalHpp);

            const profitPercent = parseFloat(document.getElementById('profit_percent').value) || 0;
            const profitAmount = totalHpp * (profitPercent / 100);
            const finalPriceRaw = totalHpp + profitAmount;
            const roundedPrice = Math.ceil(finalPriceRaw / 1000) * 1000;
            document.getElementById('final_price_display').innerText = formatRupiah(roundedPrice);
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (existingComponents.length > 0) {
                existingComponents.forEach(data => addRow(data));
            } else {
                addRow();
            }
            calculateAll();
            initDesignLightbox();
        });

        function initDesignLightbox() {
            const modalEl = document.getElementById('designLightboxModal');
            const imgEl = document.getElementById('designLightboxImg');
            const titleEl = document.getElementById('designLightboxTitle');
            const bodyEl = document.getElementById('designLightboxBody');
            if (!modalEl || !imgEl) return;

            let scale = 1;
            const setScale = (s) => {
                scale = Math.min(4, Math.max(0.5, s));
                imgEl.style.transform = 'scale(' + scale + ')';
            };

            document.querySelectorAll('.design-thumb-trigger').forEach(btn => {
                btn.addEventListener('click', () => {
                    imgEl.src = btn.dataset.src || '';
                    titleEl.textContent = btn.dataset.title || 'Preview';
                    scale = 1;
                    setScale(1);
                    new bootstrap.Modal(modalEl).show();
                });
            });

            document.getElementById('designZoomIn')?.addEventListener('click', () => setScale(scale + 0.25));
            document.getElementById('designZoomOut')?.addEventListener('click', () => setScale(scale - 0.25));
            document.getElementById('designZoomReset')?.addEventListener('click', () => setScale(1));

            bodyEl?.addEventListener('wheel', (e) => {
                if (!modalEl.classList.contains('show')) return;
                e.preventDefault();
                setScale(scale + (e.deltaY < 0 ? 0.15 : -0.15));
            }, { passive: false });
        }

        const mainForm = document.getElementById('calculatorForm');
        mainForm.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
        });
        mainForm.addEventListener('keypress', function(e) {
            if (e.keyCode === 13 && e.target.type !== 'textarea') {
                e.preventDefault();
                return false;
            }
        });
    </script>
@endpush