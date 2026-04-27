@props([
    'includeSession' => true,
    'container' => null, // 'container' | 'container-fluid' | null
    'class' => '',
])

@push('styles')
    <style>
        .alert {
            border-radius: var(--radius-lg, 12px);
            border: none;
            padding: 1rem 1.25rem;
            animation: slideInDown 0.3s ease-out;
        }

        .alert-dismissible .btn-close {
            padding: 0.25rem;
            opacity: 0.7;
            transition: opacity var(--transition, 0.3s ease);
        }

        .alert-dismissible .btn-close:hover {
            opacity: 1;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .alert-auto {
            animation-duration: 4s;
            animation-fill-mode: forwards;
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideOutUp {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(-20px);
                opacity: 0;
            }
        }

        .alert-auto.fade.show {
            animation: slideInDown 0.3s ease-out, slideOutUp 0.3s ease-in 4s forwards;
        }

        @media (max-width: 576px) {
            .alert {
                font-size: 0.9rem;
                padding: 0.75rem 1rem;
            }
        }
    </style>
@endpush

@php
    $wrapClass = trim(collect([
        $container,
        $class,
    ])->filter()->implode(' '));

    $sessionAlerts = [
        'success' => ['class' => 'alert-success', 'icon' => 'bi-check-circle-fill'],
        'error' => ['class' => 'alert-danger', 'icon' => 'bi-exclamation-triangle-fill'],
        'warning' => ['class' => 'alert-warning', 'icon' => 'bi-exclamation-circle-fill'],
        'info' => ['class' => 'alert-info', 'icon' => 'bi-info-circle-fill'],
    ];
@endphp

@if ($errors->any() || ($includeSession && collect(array_keys($sessionAlerts))->contains(fn ($k) => session()->has($k))))
    <div class="{{ $wrapClass }}">
        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show alert-auto mb-3" role="alert">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                    <div class="flex-grow-1">
                        <div class="fw-semibold mb-2">⚠️ Periksa kembali input Anda:</div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li class="mb-1">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        {{-- Session flash messages --}}
        @if ($includeSession)
            @foreach ($sessionAlerts as $key => $meta)
                @if (session()->has($key))
                    <div class="alert {{ $meta['class'] }} alert-dismissible fade show alert-auto mb-3" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi {{ $meta['icon'] }} flex-shrink-0"></i>
                            <span class="flex-grow-1">{{ session($key) }}</span>
                            <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>
@endif

