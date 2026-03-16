<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Http\Requests\Production\StoreProductionTodoRequest;
use App\Http\Requests\Production\UpdateProductionTodoRequest;
use App\Models\ProductionTodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductionTodoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:production_staff']);
        $this->authorizeResource(ProductionTodo::class, 'todo');
    }

    public function index(Request $request): View
    {
        $userId = Auth::id();

        $todos = ProductionTodo::query()
            ->forUser($userId)
            ->status($request->query('status'))
            ->searchTitle($request->query('search'))
            ->orderDefault()
            ->paginate(10)
            ->withQueryString();

        return view('production.todos.index', [
            'todos' => $todos,
            'statusOptions' => ProductionTodo::statuses(),
            'currentStatus' => $request->query('status'),
            'search' => $request->query('search'),
        ]);
    }

    public function create(): View
    {
        return view('production.todos.create', [
            'statusOptions' => ProductionTodo::statuses(),
        ]);
    }

    public function store(StoreProductionTodoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $todo = ProductionTodo::create($data);

        return redirect()
            ->route('staff.production.todos.show', $todo)
            ->with('success', 'Tugas produksi berhasil dibuat.');
    }

    public function show(ProductionTodo $todo): View
    {
        return view('production.todos.show', compact('todo'));
    }

    public function edit(ProductionTodo $todo): View
    {
        return view('production.todos.edit', [
            'todo' => $todo,
            'statusOptions' => ProductionTodo::statuses(),
        ]);
    }

    public function update(UpdateProductionTodoRequest $request, ProductionTodo $todo): RedirectResponse
    {
        $todo->update($request->validated());

        return redirect()
            ->route('staff.production.todos.show', $todo)
            ->with('success', 'Tugas produksi berhasil diperbarui.');
    }

    public function destroy(ProductionTodo $todo): RedirectResponse
    {
        $todo->delete();

        return redirect()
            ->route('staff.production.todos.index')
            ->with('success', 'Tugas produksi berhasil dihapus.');
    }

    public function updateStatus(Request $request, ProductionTodo $todo): Response|RedirectResponse
    {
        $this->authorize('update', $todo);

        $request->validate([
            'status' => ['required', 'in:' . implode(',', ProductionTodo::statuses())],
        ]);

        $todo->update([
            'status' => $request->string('status')->toString(),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response([
                'message' => 'Status tugas berhasil diperbarui.',
                'data' => [
                    'id' => $todo->id,
                    'status' => $todo->status,
                    'status_label' => $todo->status_label,
                    'status_badge_class' => $todo->status_badge_class,
                    'is_overdue' => $todo->isOverdue(),
                ],
            ]);
        }

        return redirect()
            ->route('staff.production.todos.index')
            ->with('success', 'Status tugas berhasil diperbarui.');
    }
}