@props(['title', 'subtitle' => '', 'size' => 'md'])

<div class="modal-header">
    <h{{ in_array($size, ['lg', 'xl']) ? '4' : '5' }} class="modal-title">
        {{ $title }}
    </h{{ in_array($size, ['lg', 'xl']) ? '4' : '5' }}>
    @if($subtitle)
        <small class="text-muted d-block">{{ $subtitle }}</small>
    @endif
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    {!! $slot !!}
</div>
@if(isset($footer))
<div class="modal-footer">
    {!! $footer !!}
</div>
@endif
