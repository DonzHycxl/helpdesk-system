<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Ticket;
use Filament\Widgets\ChartWidget;

class TicketsCreatedByDepartmentChart extends ChartWidget
{
    protected ?string $heading = 'Tickets Created By Department';
    
    protected int | string | array $columnSpan = 'full';

    public ?string $filter = null;

    public function mount(): void
    {
        parent::mount();
        $this->filter = (string) now()->year;
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        // Get all departments with their ticket count for the selected year
        $departments = Department::withCount(['tickets' => function ($query) use ($activeFilter) {
            $query->whereYear('datetime_reported', $activeFilter);
        }])->get();

        return [
            'datasets' => [
                [
                    'label' => 'Tickets Created',
                    'data' => $departments->pluck('tickets_count')->toArray(),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#3b82f6',
                ],
            ],
            'labels' => $departments->pluck('name')->toArray(),
        ];
    }

    protected function getFilters(): ?array
    {
        $currentYear = now()->year;
        
        $years = [];
        // Generate last 5 years
        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear - $i;
            $years[$year] = $year;
        }

        return $years;
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'stepSize' => 1,
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}