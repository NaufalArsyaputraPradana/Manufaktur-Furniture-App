@props(['title', 'message', 'icon' => 'bi-inbox', 'action' => null])

<div class="text-center py-5">
    <div class="mb-3 text-muted opacity-25">
        <i class="{{ $icon }}" style="font-size: 4rem;"></i>
    </div>
    <h5 class="text-muted fw-bold">{{ $title }}</h5>
    <p class="text-muted mb-4">{{ $message }}</p>
    @if($action)
        <a href="{{ $action['url'] }}" class="btn btn-primary px-4 shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>{{ $action['label'] }}
        </a>
    @endif
</div>
