<div class="mb-3">
    @if($label ?? false)
        <label for="{{ $name ?? '' }}" class="form-label">
            {{ $label }}
            @if($required ?? false)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    @switch($type ?? 'text')
        @case('textarea')
            <textarea 
                id="{{ $name ?? '' }}"
                name="{{ $name ?? '' }}"
                class="form-control @error($name ?? '') is-invalid @enderror"
                {{ $attributes->except(['class', 'type', 'name']) }}
                @required($required ?? false)
            >{{ old($name ?? '', $value ?? '') }}</textarea>
            @break
        
        @case('select')
            <select 
                id="{{ $name ?? '' }}"
                name="{{ $name ?? '' }}"
                class="form-select @error($name ?? '') is-invalid @enderror"
                {{ $attributes->except(['class', 'type', 'name']) }}
                @required($required ?? false)
            >
                <option value="">-- Pilih {{ $label ?? '' }} --</option>
                @foreach($options ?? [] as $key => $option)
                    <option value="{{ $key }}" @selected(old($name ?? '', $value ?? '') == $key)>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
            @break
        
        @case('checkbox')
            <div class="form-check">
                <input 
                    type="checkbox"
                    id="{{ $name ?? '' }}"
                    name="{{ $name ?? '' }}"
                    class="form-check-input @error($name ?? '') is-invalid @enderror"
                    value="{{ $value ?? '1' }}"
                    {{ $attributes->except(['class', 'type', 'name']) }}
                    @checked(old($name ?? '', $checked ?? false))
                >
                <label class="form-check-label" for="{{ $name ?? '' }}">
                    {{ $label ?? '' }}
                </label>
            </div>
            @break
        
        @default
            <input 
                type="{{ $type ?? 'text' }}"
                id="{{ $name ?? '' }}"
                name="{{ $name ?? '' }}"
                class="form-control @error($name ?? '') is-invalid @enderror"
                value="{{ old($name ?? '', $value ?? '') }}"
                {{ $attributes->except(['class', 'type', 'name']) }}
                @required($required ?? false)
            >
    @endswitch
    
    @error($name ?? '')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
    
    @if($help ?? false)
        <small class="form-text text-muted d-block mt-1">
            {{ $help }}
        </small>
    @endif
</div>
