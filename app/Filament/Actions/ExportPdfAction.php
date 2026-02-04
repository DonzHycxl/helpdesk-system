<?php

namespace App\Filament\Actions;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Text;

class ExportPdfAction extends Action
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
            ->modalHeading('Export Tickets Summary to PDF')
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
                    ->with(['status', 'department', 'priority', 'assignedTo', 'createdBy'])
                    ->whereBetween('datetime_reported', [$startDate, $endDate])
                    ->orderBy('datetime_reported')
                    ->get();

                // Calculate summary
                $summary = $this->calculateSummary($tickets, $startDate, $endDate);

                // Generate PDF
                $pdf = Pdf::loadView('exports.tickets-pdf', [
                    'tickets' => $tickets,
                    'summary' => $summary,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                ]);

                $pdf->setPaper('a4');

                $filename = 'tickets_report_' . $startDate->format('d-m-Y') . '_to_' . $endDate->format('d-m-Y') . '.pdf';

                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf->output();
                }, $filename);
            });
    }

    protected function calculateSummary($tickets, $startDate, $endDate): array
    {
        // Group tickets by month
        $monthlyData = [];

        $currentMonth = $startDate->copy()->startOfMonth();
        $lastMonth = $endDate->copy()->startOfMonth();

        // Initialize all months in range
        while ($currentMonth <= $lastMonth) {
            $monthKey = $currentMonth->format('Y-m');
            $monthlyData[$monthKey] = [
                'month' => $currentMonth->format('F'),
                'year' => $currentMonth->format('Y'),
                'closed' => 0,
                'hold' => 0,
                'open' => 0,
                'all' => 0,
            ];
            $currentMonth->addMonth();
        }

        // Count tickets by status for each month
        foreach ($tickets as $ticket) {
            $ticketMonth = Carbon::parse($ticket->datetime_reported)->format('Y-m');
            $statusName = $ticket->status?->name;

            if (!isset($monthlyData[$ticketMonth])) {
                continue;
            }

            $monthlyData[$ticketMonth]['all']++;

            // Map status to category
            if ($statusName === 'Closed (Resolved)') {
                $monthlyData[$ticketMonth]['closed']++;
            } elseif ($statusName === 'Hold (In Progress)') {
                $monthlyData[$ticketMonth]['hold']++;
            } elseif ($statusName === 'Open (Unresolved)') {
                $monthlyData[$ticketMonth]['open']++;
            }
        }

        // Calculate percentages for each month
        foreach ($monthlyData as &$data) {
            if ($data['all'] > 0) {
                $data['closed_percent'] = round(($data['closed'] / $data['all']) * 100);
                $data['hold_percent'] = round(($data['hold'] / $data['all']) * 100);
                $data['open_percent'] = round(($data['open'] / $data['all']) * 100);
            } else {
                $data['closed_percent'] = 0;
                $data['hold_percent'] = 0;
                $data['open_percent'] = 0;
            }
        }

        // Calculate totals across all statuses
        $totalTickets = $tickets->count();
        $closedTickets = $tickets->filter(fn($t) => $t->status?->name === 'Closed (Resolved)')->count();
        $openTickets = $tickets->filter(fn($t) => $t->status?->name === 'Open (Unresolved)')->count();
        $holdTickets = $tickets->filter(fn($t) => $t->status?->name === 'Hold (In Progress)')->count();

        return [
            'monthly_data' => array_values($monthlyData),
            'total_tickets' => $totalTickets,
            'closed_tickets' => $closedTickets,
            'open_tickets' => $openTickets,
            'hold_tickets' => $holdTickets,
        ];
    }
}
