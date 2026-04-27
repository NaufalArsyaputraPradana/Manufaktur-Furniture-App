@props(['text' => '', 'value' => '0'])

<span {{ $attributes->merge(['class' => 'badge bg-primary']) }}>
    @if($text)
        {{ $text }}
    @endif
    @if($value)
        <span class="badge bg-danger rounded-pill ms-1">{{ $value }}</span>
    @endif
    {!! $slot !!}
</span>
