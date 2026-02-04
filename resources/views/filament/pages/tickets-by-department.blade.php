<x-filament-panels::page>
    @livewire(\App\Livewire\TicketsCreatedByDepartmentChart::class)

    <div class="flex justify-end mt-4">
        {{ $this->exportPdf }}
    </div>
</x-filament-panels::page>