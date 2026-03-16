@if(session('success'))
    <x-alert type="success" class="mb-4">
        <strong>Success!</strong> {{ session('success') }}
    </x-alert>
@endif

@if(session('error'))
    <x-alert type="error" class="mb-4">
        <strong>Error!</strong> {{ session('error') }}
    </x-alert>
@endif

@if(session('warning'))
    <x-alert type="warning" class="mb-4">
        <strong>Warning!</strong> {{ session('warning') }}
    </x-alert>
@endif

@if(session('info'))
    <x-alert type="info" class="mb-4">
        <strong>Info!</strong> {{ session('info') }}
    </x-alert>
@endif

@if($errors->any())
    <x-alert type="error" class="mb-4">
        <strong>Validation Error!</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif
