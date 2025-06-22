<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div class="type-select-wrapper">
        @foreach($getOptions() as $key => $value)
            {{-- Generate a unique ID for each radio button to prevent conflicts --}}
            @php
                $radioId = $getId() . '-' . $key;
            @endphp
            <label
                for="{{ $radioId }}"
                class="type-select-option-label-wrapper @if($getState() === $key) type-select-option-selected @endif"
            >
                <input
                    type="radio"
                    id="{{ $radioId }}"
                    name="{{ $getId() }}"
                    value="{{ $key }}"
                    wire:model="{{ $getStatePath() }}"
                    @disabled($isDisabled())
                    {{ $attributes->class(['sr-only']) }}
                >
                <div class="type-select-option" data-key="{{ $key }}">
                    <div class="type-select-option-icon">
                        @svg($getIconByOptionValue($key))
                    </div>
                    <div class="type-select-option-text">
                        {{ $value }}
                    </div>
                </div>
            </label>
        @endforeach
    </div>
</x-dynamic-component>
