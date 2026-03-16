@props([
    'id' => 'modal',
    'title' => 'Modal Title',
    'size' => 'md',
    'centered' => false,
    'scrollable' => false,
    'staticBackdrop' => false,
])

@php
    $sizeClass = match($size) {
        'sm' => 'modal-sm',
        'lg' => 'modal-lg',
        'xl' => 'modal-xl',
        default => '',
    };
    
    $modalDialogClass = "modal-dialog {$sizeClass}";
    if ($centered) $modalDialogClass .= ' modal-dialog-centered';
    if ($scrollable) $modalDialogClass .= ' modal-dialog-scrollable';
@endphp

<div 
    class="modal fade" 
    id="{{ $id }}" 
    tabindex="-1" 
    aria-labelledby="{{ $id }}Label" 
    aria-hidden="true"
    @if($staticBackdrop) data-bs-backdrop="static" data-bs-keyboard="false" @endif
>
    <div class="{{ $modalDialogClass }}">
        <div class="modal-content">
            <div class="modal-header">
                @if(isset($header))
                    {{ $header }}
                @else
                    <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                @endif
            </div>
            
            <div class="modal-body">
                {{ $slot }}
            </div>
            
            @if(isset($footer))
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
