<div class="card {{ $class ?? '' }}" {{ $attributes->except('class') }}>
    @if($title ?? false)
        <div class="card-header {{ $headerClass ?? 'bg-light' }}">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ $title }}</h5>
                @if($headerAction ?? false)
                    {{ $headerAction }}
                @endif
            </div>
        </div>
    @endif

    <div class="card-body {{ $bodyClass ?? '' }}">
        {{ $slot }}
    </div>

    @if($footer ?? false)
        <div class="card-footer {{ $footerClass ?? 'bg-light' }}">
            {{ $footer }}
        </div>
    @endif
</div>
