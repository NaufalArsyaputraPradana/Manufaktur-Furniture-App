@props([
    'title' => 'No Data',
    'message' => 'There is no data to display.',
    'icon' => 'inbox',
    'action' => null,
    'actionLabel' => null,
])

<div {{ $attributes->merge(['class' => 'text-center py-5']) }}>
    <i class="bi bi-{{ $icon }} text-muted mb-3" style="font-size: 4rem;"></i>
    
    <h4 class="text-muted">{{ $title }}</h4>
    
    <p class="text-muted mb-4">{{ $message }}</p>
    
    @if($action)
        <a href="{{ $action }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            {{ $actionLabel ?? 'Add New' }}
        </a>
    @endif
    
    {{ $slot }}
</div>
