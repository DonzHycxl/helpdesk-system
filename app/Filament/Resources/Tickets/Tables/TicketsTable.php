<?php

namespace App\Filament\Resources\Tickets\Tables;

use App\Filament\Actions\ExportPdfAction;
use App\Filament\Exports\TicketExporter;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priority.name')
                    ->label('Priority')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Critical' => 'danger',
                        'Semi-critical' => 'warning',
                        'Non-critical' => 'success',
                        'General Enquiries' => 'gray',
                        'Change Request' => 'primary',
                    }),
                TextColumn::make('subject')
                    ->searchable()
                    ->sortable()
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->since()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->since()
                    ->sortable(),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, 'Open') => 'warning',
                        str_contains($state, 'Hold') => 'info',
                        str_contains($state, 'Closed') => 'success',
                    }),
            ])
            ->filters([
                SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->label('Department')
                    ->native(false),
                SelectFilter::make('priority_id')
                    ->relationship('priority', 'name')
                    ->label('Priority')
                    ->native(false),
                SelectFilter::make('status_id')
                    ->relationship('status', 'name', fn (Builder $query) => $query->orderBy('id'))
                    ->label('Status')
                    ->native(false),
                SelectFilter::make('assigned_to_user_id')
                    ->relationship('assignedTo', 'name')
                    ->label('Assigned To')
                    ->native(false),
            ], layout: FiltersLayout::Modal)
            ->defaultSort('id')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                ]),
                //ExportPdfAction::make(),
                ExportAction::make()
                    ->exporter(TicketExporter::class)
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->columnMapping(false)
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-arrow-down-tray')
                    ->modalDescription('Select date range and export options')
                    ->fileName(function (array $data): string {
                        if (!empty($data['start_date']) && !empty($data['end_date'])) {
                            $startDate = Carbon::parse($data['start_date'])->format('Y-m-d');
                            $endDate = Carbon::parse($data['end_date'])->format('Y-m-d');
                            return "tickets_{$startDate}_to_{$endDate}";
                        }
                        return 'tickets_export_' . now()->format('Y-m-d');
                    })
                    ->modifyQueryUsing(function (Builder $query, array $data) {
                        if (!empty($data['start_date']) && !empty($data['end_date'])) {
                            $startDate = Carbon::parse($data['start_date'])->startOfDay();
                            $endDate = Carbon::parse($data['end_date'])->endOfDay();
                            return $query->whereBetween('datetime_reported', [$startDate, $endDate]);
                        }
                        return $query;
                    })
            ]);
    }
}
