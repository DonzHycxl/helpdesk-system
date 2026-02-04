<?php

namespace App\Filament\Actions;

use App\Models\Department;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Text;

class ExportDepartmentPdfAction extends Action
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
            ->modalHeading('Export Tickets Summary by Department')
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
                    ->get();

                // Calculate summary by department
                $summary = $this->calculateSummary($tickets);

                // Generate PDF
                $pdf = Pdf::loadView('exports.tickets-department-pdf', [
                    'tickets' => $tickets,
                    'summary' => $summary,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                ]);

                $pdf->setPaper('a4');

                $filename = 'tickets_department_report_' . $startDate->format('d-m-Y') . '_to_' . $endDate->format('d-m-Y') . '.pdf';

                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf->output();
                }, $filename);
            });
    }

    protected function calculateSummary($tickets): array
    {
        $departmentData = [];

        // Get all departments to ensure they are all listed even if they have no tickets
        $allDepartments = Department::orderBy('name')->get();

        foreach ($allDepartments as $dept) {
            $departmentData[$dept->name] = [
                'name' => $dept->name,
                'closed' => 0,
                'hold' => 0,
                'open' => 0,
                'all' => 0,
            ];
        }

        // Count tickets by status for each department
        foreach ($tickets as $ticket) {
            $deptName = $ticket->department?->name ?? 'Unassigned';
            $statusName = $ticket->status?->name;

            if (!isset($departmentData[$deptName])) {
                $departmentData[$deptName] = [
                    'name' => $deptName,
                    'closed' => 0,
                    'hold' => 0,
                    'open' => 0,
                    'all' => 0,
                ];
            }

            $departmentData[$deptName]['all']++;

            if ($statusName === 'Closed (Resolved)') {
                $departmentData[$deptName]['closed']++;
            } elseif ($statusName === 'Hold (In Progress)') {
                $departmentData[$deptName]['hold']++;
            } elseif ($statusName === 'Open (Unresolved)') {
                $departmentData[$deptName]['open']++;
            }
        }

        // Calculate percentages
        foreach ($departmentData as &$data) {
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

        $totalTickets = $tickets->count();
        $closedTickets = $tickets->filter(fn($t) => $t->status?->name === 'Closed (Resolved)')->count();
        $openTickets = $tickets->filter(fn($t) => $t->status?->name === 'Open (Unresolved)')->count();
        $holdTickets = $tickets->filter(fn($t) => $t->status?->name === 'Hold (In Progress)')->count();

        return [
            'department_data' => array_values($departmentData),
            'total_tickets' => $totalTickets,
            'closed_tickets' => $closedTickets,
            'open_tickets' => $openTickets,
            'hold_tickets' => $holdTickets,
        ];
    }
}
