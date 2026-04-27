<div class="table-responsive">
    <table class="table {{ $class ?? 'table-hover table-striped' }}" {{ $attributes->except('class') }}>
        @if($headers ?? false)
            <thead class="table-{{ $headerClass ?? 'light' }}">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif
        
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
