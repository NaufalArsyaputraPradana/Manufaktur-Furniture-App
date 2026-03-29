@extends('layouts.production')

@section('title', 'Jadwal Produksi')

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Jadwal Produksi</h4>
            <p class="text-muted mb-0 small">Jadwal tugas produksi Anda</p>
        </div>
        <a href="{{ route('staff.production.schedules.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Jadwal Baru
        </a>
    </div>

            {{-- Filter Form --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-8">
                            <x-form-input
                                name="search"
                                label="Cari Judul"
                                type="text"
                                :value="$search"
                                placeholder="Cari jadwal..."
                            />
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-4">
                {{-- List View --}}
                <div class="col-lg-5">
                    @if ($schedules->count())
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between">
                                <h5 class="fw-bold mb-0"><i class="bi bi-list-task text-primary me-2"></i>Daftar Jadwal</h5>
                                <span class="badge bg-primary">Total {{ $schedules->total() }}</span>
                            </div>
                            <div class="list-group list-group-flush">
                                @foreach ($schedules as $schedule)
                                    <a href="{{ route('staff.production.schedules.show', $schedule) }}"
                                        class="list-group-item list-group-item-action p-3">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="fw-bold mb-1">{{ $schedule->title }}</h6>
                                            @if ($schedule->isPast())
                                                <span class="badge bg-secondary">Selesai</span>
                                            @else
                                                <span class="badge bg-success">Aktif</span>
                                            @endif
                                        </div>
                                        <small class="text-muted d-block">
                                            <i
                                                class="bi bi-clock me-1"></i>{{ $schedule->start_datetime->translatedFormat('d M Y H:i') }}
                                            – {{ $schedule->end_datetime->translatedFormat('d M Y H:i') }}
                                        </small>
                                        @if ($schedule->location)
                                            <small class="text-muted"><i
                                                    class="bi bi-geo-alt me-1"></i>{{ $schedule->location }}</small>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                            @if ($schedules->hasPages())
                                <div class="card-footer bg-white border-0 py-3">
                                    {{ $schedules->links() }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                            <div class="card-body">
                                <i class="bi bi-calendar-x display-1 text-muted opacity-50"></i>
                                <h5 class="mt-3">Belum ada jadwal</h5>
                                <p class="text-muted">Buat jadwal untuk mengorganisir pekerjaan produksi Anda.</p>
                                <a href="{{ route('staff.production.schedules.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-circle me-2"></i>Buat Jadwal Pertama
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Calendar View --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="fw-bold mb-0"><i class="bi bi-calendar-week text-primary me-2"></i>Kalender</h5>
                        </div>
                        <div class="card-body p-0">
                            <div id="production-calendar" style="min-height: 500px; padding: 1rem;"></div>
                        </div>
                    </div>
                </div>
            </div>
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <style>
        .fc .fc-toolbar-title {
            font-size: 1.1rem;
            font-weight: 600;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('production-calendar');
            if (calendarEl) {
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    height: 550,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    locale: 'id',
                    firstDay: 1,
                    events: '{{ route('staff.production.schedules.events') }}',
                    eventClick: function(info) {
                        if (info.event.id) {
                            window.location.href =
                                '{{ route('staff.production.schedules.show', ':id') }}'.replace(':id',
                                    info.event.id);
                        }
                    },
                    displayEventEnd: true,
                });
                calendar.render();
            }
        });
    </script>
@endpush
