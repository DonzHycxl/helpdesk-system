<?php

namespace App\Filament\Actions;

use App\Models\Priority;
use App\Models\Status;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Text;

class ExportPriorityVsStatusPdfAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'exportPdf';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Export summary')
            ->icon('heroicon-o-document-arrow-down')
            ->color('primary')
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-document-arrow-down')
            ->modalHeading('Export Tickets Priority vs Status')
            ->modalDescription('Select date range to generate a PDF summary report')
            ->modalSubmitActionLabel('Generate PDF')
            ->schema([
                Grid::make()
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Select start date')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Select end date')
                            ->required(),
                    ]),
                Text::make('A PDF report will be generated with summary statistics.')
                    ->extraAttributes(['class' => 'text-center w-full block flex justify-center'])
            ])
            ->action(function (array $data) {
                $startDate = Carbon::parse($data['start_date'])->startOfDay();
                $endDate = Carbon::parse($data['end_date'])->endOfDay();

                // Fetch tickets within date range
                $tickets = Ticket::query()
                    ->with(['status', 'priority'])
                    ->whereBetween('datetime_reported', [$startDate, $endDate])
                    ->get();

                // Calculate summary
                $summary = $this->calculateSummary($tickets);

                // Generate PDF
                $pdf = Pdf::loadView('exports.tickets-priority-status-pdf', [
                    'tickets' => $tickets,
                    'summary' => $summary,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                ]);

                $pdf->setPaper('a4');

                $filename = 'tickets_priority_status_' . $startDate->format('d-m-Y') . '_to_' . $endDate->format('d-m-Y') . '.pdf';

                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf->output();
                }, $filename);
            });
    }

    protected function calculateSummary($tickets): array
    {
        $priorities = Priority::orderBy('id')->get();
        $statuses = Status::all();

        $matrix = [];
        $priorityTotals = [];
        $statusTotals = [];

        // Initialize matrix
        foreach ($priorities as $priority) {
            $priorityTotals[$priority->name] = 0;
            foreach ($statuses as $status) {
                $matrix[$priority->name][$status->name] = 0;
                $statusTotals[$status->name] = 0;
            }
        }

        // Fill matrix
        foreach ($tickets as $ticket) {
            $pName = $ticket->priority?->name ?? 'Unassigned';
            $sName = $ticket->status?->name ?? 'Unassigned';

            if (isset($matrix[$pName][$sName])) {
                $matrix[$pName][$sName]++;
                $priorityTotals[$pName]++;
                $statusTotals[$sName]++;
            }
        }

        // Calculate Percentages for Visuals
        $visualData = [];
        foreach ($priorities as $priority) {
            $pName = $priority->name;
            $total = $priorityTotals[$pName];
            
            $visualData[$pName] = [
                'total' => $total,
                'segments' => []
            ];

            if ($total > 0) {
                foreach ($statuses as $status) {
                    $sName = $status->name;
                    $count = $matrix[$pName][$sName];
                    $percent = round(($count / $total) * 100, 1);
                    
                    if ($count > 0) {
                        $visualData[$pName]['segments'][] = [
                            'status' => $sName,
                            'count' => $count,
                            'percent' => $percent
                        ];
                    }
                }
            }
        }

        return [
            'matrix' => $matrix,
            'priorities' => $priorities->pluck('name')->toArray(),
            'statuses' => $statuses->pluck('name')->toArray(),
            'priority_totals' => $priorityTotals,
            'status_totals' => $statusTotals,
            'visual_data' => $visualData,
            'total_tickets' => $tickets->count(),
        ];
    }
}
