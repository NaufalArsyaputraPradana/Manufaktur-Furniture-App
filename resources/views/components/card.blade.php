@props([
    'title' => null,
    'footer' => null,
    'headerClass' => '',
    'bodyClass' => '',
    'footerClass' => '',
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title || isset($header))
        <div class="card-header {{ $headerClass }}">
            @if(isset($header))
                {{ $header }}
            @else
                <h5 class="card-title mb-0">{{ $title }}</h5>
            @endif
        </div>
    @endif
    
    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>
    
    @if($footer || isset($cardFooter))
        <div class="card-footer {{ $footerClass }}">
            {{ $cardFooter ?? $footer }}
        </div>
    @endif
</div>
