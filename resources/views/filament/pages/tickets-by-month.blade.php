<x-filament-panels::page>
    @livewire(\App\Livewire\TicketsCreatedByMonthChart::class)

    <div class="flex justify-end mt-4">
        {{ $this->exportPdf }}
    </div>
</x-filament-panels::page>