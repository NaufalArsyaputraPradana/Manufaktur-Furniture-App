@extends('layouts.production')

@section('title', 'Edit Tugas Produksi')

@push('styles')
<style>
    :root { --prod-primary: #10b981; --prod-secondary: #059669; }
    .text-shadow { text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4">
    {{-- Page Header --}}
    <div class="card border-0 shadow-sm mb-5 overflow-hidden" style="background: linear-gradient(135deg, var(--prod-primary) 0%, var(--prod-secondary) 100%);">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.1); z-index: 1;"></div>
        <div class="card-body text-white py-4 position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7 mb-3 mb-md-0">
                    <h2 class="fw-bold mb-2 text-shadow">Edit Tugas Produksi</h2>
                    <p class="text-white text-opacity-90 mb-0"><i class="bi bi-pencil-square me-1"></i>Perbarui informasi tugas</p>
                </div>
                <div class="col-lg-4 col-md-5 text-lg-end">
                    <a href="{{ route('staff.production.todos.index') }}" class="btn btn-light shadow-sm text-primary fw-bold">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-4">
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
