<?php

namespace App\Filament\Exports;

use App\Models\Ticket;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Text;
use Illuminate\Support\Number;

class TicketExporter extends Exporter
{
    protected static ?string $model = Ticket::class;

    public static function getOptionsFormComponents(): array
    {
        return [
            Grid::make()
                ->schema([
                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->placeholder('Select start date'),
                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->placeholder('Select end date'),
                ]),
            Text::make('Leave both dates empty to export all tickets.')
                ->extraAttributes(['class' => 'text-center w-full block flex justify-center'])
        ];
    }

    public function getFormats(): array
    {
        return [
            ExportFormat::Csv
        ];
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('TIX'),
            ExportColumn::make('priority.id')
                ->label('PRIORITY'),
            ExportColumn::make('status.name')
                ->label('STATUS'),
            ExportColumn::make('datetime_reported')
                ->label('DATETIME CREATED'),
            ExportColumn::make('datetime_closed')
                ->label('DATETIME CLOSED'),
            ExportColumn::make('subject')
                ->label('SUBJECT'),
            ExportColumn::make('description')
                ->label('DESCRIPTION'),
            ExportColumn::make('responses')
                ->label('ACTION TAKEN')
                ->state(fn ($record) => $record->responses->pluck('response')->implode("\n")),
            ExportColumn::make('assignedTo')
                ->label('ENGINEER')
                ->state(fn ($record) => $record->assignedTo?->name),
            ExportColumn::make('department.name')
                ->label('MODULES'),
            ExportColumn::make('duration')
                ->label('DURATION')
                ->state(function ($record) {
                    if (! $record->datetime_closed || ! $record->datetime_reported) {
                        return null;
                    }

                    return $record->datetime_reported->diff($record->datetime_closed)->format('%d Hari, %h Jam, %i Minit');
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your ticket export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
