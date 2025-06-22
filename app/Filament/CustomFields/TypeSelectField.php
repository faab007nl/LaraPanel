<?php

namespace App\Filament\CustomFields;

use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasIcon;
use Filament\Tables\Filters\Concerns\HasOptions;

class TypeSelectField extends Field
{
    use HasOptions;
    use HasIcon;

    protected string $view = 'filament.custom-fields.type-select.select';

    public array $icons = [];

    public function icons(array $icons): static
    {
        $this->icons = $icons;
        return $this;
    }

    public function getIconByOptionValue(string $value): string
    {
        $icon = $this->icons[$value] ?? null; // Use null coalescing for safer access
        if (is_string($icon)) {
            return $icon;
        }
        if (is_array($icon) && isset($icon['icon'])) {
            return $icon['icon'];
        }
        return 'heroicon-o-question-mark-circle'; // Default icon if not found
    }

    /**
     * Set the default value for the field.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set a default value if not already set, e.g., the first option's key.
        $this->default(array_key_first($this->getOptions() ?? []));

        // You might want to ensure a value is always present in the form state.
        $this->dehydrated(); // Ensures the field's value is always included in the form's data.
    }

}
