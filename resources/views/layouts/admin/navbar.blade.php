{{-- ================= LEFT SECTION ================= --}}
<div class="d-flex align-items-center gap-3">

    {{-- Sidebar Toggle (Mobile) --}}
    <button class="btn btn-light d-lg-none border-0 shadow-sm" id="sidebarToggle" type="button"
        aria-label="Toggle Sidebar">
        <i class="bi bi-list fs-4"></i>
    </button>

    {{-- Page Title --}}
    <h5 class="mb-0 fw-bold text-dark">
        @yield('title', 'Dashboard')
    </h5>

</div>

{{-- ================= RIGHT SECTION ================= --}}
<div class="d-flex align-items-center gap-3">

    {{-- NOTIFICATION --}}
    @php
        $pendingPaymentsCount = \App\Models\Payment::where('payment_status', \App\Models\Payment::STATUS_UNPAID)
            ->whereNotNull('payment_proof')->count();
        $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();
        $totalNotifCount = $pendingPaymentsCount + $pendingOrdersCount;
    @endphp
    <div class="dropdown">
        <button class="btn btn-light position-relative border-0 shadow-sm rounded-circle" type="button"
            id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 42px; height: 42px;">

            <i class="bi bi-bell fs-5"></i>

            @if ($totalNotifCount > 0)
                <span
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light">
                    {{ $totalNotifCount > 99 ? '99+' : $totalNotifCount }}
                </span>
            @endif
        </button>

        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="notifDropdown"
            style="width: 320px;">
            <li class="dropdown-header text-uppercase text-secondary fw-bold small">
                Notifikasi ({{ $totalNotifCount }})
            </li>

            @if ($pendingPaymentsCount > 0)
                <li>
                    <a class="dropdown-item d-flex gap-3 py-2" href="{{ route('admin.payments.pending') }}">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 35px; height: 35px;">
                            <i class="bi bi-credit-card-fill"></i>
                        </div>
                        <div>
                            <p class="mb-0 fw-semibold small">Bukti Pembayaran Menunggu Verifikasi</p>
                            <small class="text-muted">{{ $pendingPaymentsCount }} pembayaran perlu dikonfirmasi</small>
                        </div>
                    </a>
                </li>
            @endif

            @if ($pendingOrdersCount > 0)
                <li>
                    <a class="dropdown-item d-flex gap-3 py-2" href="{{ route('admin.orders.index') }}?status=pending">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 35px; height: 35px;">
                            <i class="bi bi-cart-fill"></i>
                        </div>
                        <div>
                            <p class="mb-0 fw-semibold small">Pesanan Baru Menunggu</p>
                            <small class="text-muted">{{ $pendingOrdersCount }} pesanan menunggu konfirmasi</small>
                        </div>
                    </a>
                </li>
            @endif

            @if ($totalNotifCount === 0)
                <li>
                    <div class="dropdown-item text-center py-3 text-muted">
                        <i class="bi bi-check-circle-fill text-success d-block fs-3 mb-1"></i>
                        <small>Tidak ada notifikasi baru</small>
                    </div>
                </li>
            @endif

            <li>
                <hr class="dropdown-divider">
            </li>

            <li>
                <a class="dropdown-item text-center small text-primary fw-bold py-2" href="{{ route('admin.orders.index') }}">
                    Lihat Semua Pesanan
                </a>
            </li>
        </ul>
    </div>

    {{-- PROFILE --}}
    <div class="dropdown">

        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle gap-2"
            id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">

            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                style="width: 42px; height: 42px;">
                {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
            </div>

            <div class="d-none d-md-block lh-1">
                <span class="d-block fw-bold small text-dark">
                    {{ auth()->user()?->name ?? 'Guest User' }}
                </span>
                <span class="d-block text-secondary" style="font-size: 0.75rem;">
                    {{ ucfirst(auth()->user()?->role?->name ?? 'Admin') }}
                </span>
            </div>

        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="profileDropdown">
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.profile.index') }}">
                    <i class="bi bi-person fs-5"></i> Profil Saya
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.settings.index') }}">
                    <i class="bi bi-gear fs-5"></i> Pengaturan
                </a>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="dropdown-item d-flex align-items-center gap-2 text-danger delete-confirm-logout">
                        <i class="bi bi-box-arrow-right fs-5"></i> Keluar
                    </button>
                </form>
            </li>
        </ul>

    </div>

</div>
