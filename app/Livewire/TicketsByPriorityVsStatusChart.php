<?php

namespace App\Livewire;

use App\Models\Priority;
use App\Models\Status;
use App\Models\Ticket;
use Filament\Widgets\ChartWidget;

class TicketsByPriorityVsStatusChart extends ChartWidget
{
    protected ?string $heading = 'Tickets by Priority vs Status';

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

        $priorities = Priority::orderBy('id')->get();
        $statuses = Status::all();

        $datasets = [];

        // Define some colors for common statuses
        $statusColors = [
            'Closed (Resolved)' => '#90ee90', // Green
            'Hold (In Progress)' => '#add8e6', // Blue
            'Open (Unresolved)' => '#ffa07a', // Orange/Red
        ];

        foreach ($statuses as $status) {
            $data = [];
            foreach ($priorities as $priority) {
                $count = Ticket::query()
                    ->whereYear('datetime_reported', $activeFilter)
                    ->where('priority_id', $priority->id)
                    ->where('status_id', $status->id)
                    ->count();
                $data[] = $count;
            }

            $datasets[] = [
                'label' => $status->name,
                'data' => $data,
                'backgroundColor' => $statusColors[$status->name] ?? '#' . substr(md5($status->name), 0, 6),
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $priorities->pluck('id')->toArray(),
        ];
    }

    protected function getFilters(): ?array
    {
        $currentYear = now()->year;
        $years = [];
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
                'x' => [
                    /*'title' => [
                        'display' => true,
                        'text' => 'Priority',
                    ]*/
                ],
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
