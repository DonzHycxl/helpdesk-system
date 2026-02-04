<?php

namespace App\Livewire;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TicketsCreatedByMonthChart extends ChartWidget
{
    protected ?string $heading = 'Tickets Created By Month';
    
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

        // Get tickets for the selected year
        $tickets = Ticket::query()
            ->whereYear('datetime_reported', $activeFilter)
            ->get();

        // Group by month name
        $grouped = $tickets->groupBy(function ($ticket) {
            return Carbon::parse($ticket->datetime_reported)->format('M');
        });

        // Initialize all months (Jan-Dec)
        $months = collect(range(1, 12))->map(function ($month) {
            return Carbon::create(null, $month, 1)->format('M');
        });

        $data = $months->map(function ($month) use ($grouped) {
            return $grouped->has($month) ? $grouped->get($month)->count() : 0;
        });

        return [
            'datasets' => [
                [
                    'label' => 'Tickets Created',
                    'data' => $data->toArray(),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#3b82f6',
                ],
            ],
            'labels' => $months->toArray(),
        ];
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

    protected function getType(): string
    {
        return 'bar';
    }
}