@extends('layouts.production')

@section('title', 'Edit Tugas Produksi')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Edit Tugas Produksi</h4>
            <p class="text-muted mb-0 small">Perbarui informasi tugas</p>
        </div>
        <a href="{{ route('staff.production.todos.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Tugas</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('staff.production.todos.update', $todo) }}" method="POST" id="todoEditForm">
                        @csrf
                        @method('PUT')
                        <x-form-input 
                            name="title" 
                            label="Judul Tugas"
                            :value="old('title', $todo->title)"
                            :errors="$errors"
                            required />

                        <x-form-input 
                            name="description" 
                            label="Deskripsi"
                            type="textarea"
                            :value="old('description', $todo->description)"
                            rows="4"
                            :errors="$errors" />

                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-form-input 
                                    name="deadline" 
                                    label="Deadline"
                                    type="datetime-local"
                                    :value="old('deadline', optional($todo->deadline)->format('Y-m-d\TH:i'))"
                                    :errors="$errors" />
                            </div>
                            <div class="col-md-6">
                                <x-form-input 
                                    name="status" 
                                    label="Status"
                                    type="select"
                                    :options="collect($statusOptions)->mapWithKeys(function($status) { return [$status => ucfirst(str_replace('_', ' ', $status))]; })"
                                    :value="old('status', $todo->status)"
                                    :errors="$errors" />
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('staff.production.todos.show', $todo) }}"
                                class="btn btn-light border px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4" id="todoUpdateBtn">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
</div>
@endsection

@push('scripts')
    <script>
        document.getElementById('todoEditForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('todoUpdateBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
            }
        });
    </script>
@endpush
