<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Http\Requests\Production\StoreProductionScheduleRequest;
use App\Http\Requests\Production\UpdateProductionScheduleRequest;
use App\Models\ProductionSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductionScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:production_staff']);
        $this->authorizeResource(ProductionSchedule::class, 'schedule');
    }

    public function index(Request $request): View
    {
        $userId = Auth::id();
        $search = $request->query('search');

        $schedules = ProductionSchedule::query()
            ->forUser($userId)
            ->searchTitle($search)
            ->orderDefault()
            ->paginate(10)
            ->withQueryString();

        return view('production.schedules.index', [
            'schedules' => $schedules,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('production.schedules.create');
    }

    public function store(StoreProductionScheduleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $schedule = ProductionSchedule::create($data);

        return redirect()
            ->route('staff.production.schedules.show', $schedule)
            ->with('success', 'Jadwal produksi berhasil dibuat.');
    }

    public function show(ProductionSchedule $schedule): View
    {
        return view('production.schedules.show', compact('schedule'));
    }

    public function edit(ProductionSchedule $schedule): View
    {
        return view('production.schedules.edit', compact('schedule'));
    }

    public function update(UpdateProductionScheduleRequest $request, ProductionSchedule $schedule): RedirectResponse
    {
        $schedule->update($request->validated());

        return redirect()
            ->route('staff.production.schedules.show', $schedule)
            ->with('success', 'Jadwal produksi berhasil diperbarui.');
    }

    public function destroy(ProductionSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()
            ->route('staff.production.schedules.index')
            ->with('success', 'Jadwal produksi berhasil dihapus.');
    }

    public function events(Request $request): Response
    {
        $userId = Auth::id();
        $start = $request->query('start');
        $end = $request->query('end');

        $query = ProductionSchedule::query()->forUser($userId);

        if ($start && $end) {
            $query->between($start, $end);
        }

        $events = $query->get()->map->toFullCalendarEvent();

        return response($events);
    }

    public function exportIcs(ProductionSchedule $schedule): StreamedResponse
    {
        $filename = 'production-schedule-' . $schedule->id . '.ics';
        $content = $schedule->toIcsString();

        return response()->streamDownload(
            function () use ($content) {
                echo $content;
            },
            $filename,
            ['Content-Type' => 'text/calendar; charset=utf-8']
        );
    }
}