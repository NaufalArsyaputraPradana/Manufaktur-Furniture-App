@props(['icon' => 'fas fa-info-circle', 'title' => 'Info', 'message' => '', 'dismissible' => true])

<div class="alert alert-info {{ $dismissible ? 'alert-dismissible fade show' : '' }}" role="alert">
    @if($icon)
        <i class="{{ $icon }} me-2"></i>
    @endif
    <strong>{{ $title }}</strong>
    @if($message)
        <br>{{ $message }}
    @else
        {!! $slot !!}
    @endif
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
