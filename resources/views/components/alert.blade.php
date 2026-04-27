<div class="alert alert-{{ $type ?? 'info' }} alert-dismissible fade show" role="alert" {{ $attributes }}>
    @if($icon ?? false)
        <i class="{{ $icon }} me-2"></i>
    @endif
    
    {{ $slot }}
    
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
