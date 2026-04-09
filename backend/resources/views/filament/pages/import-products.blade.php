<x-filament-panels::page>
    <form wire:submit="runImport">
        {{ $this->form }}
        <div class="mt-6">
            <x-filament::button type="submit">بدء الاستيراد</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

