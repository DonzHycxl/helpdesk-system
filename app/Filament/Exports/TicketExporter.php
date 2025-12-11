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
                ->label('ID'),
            ExportColumn::make('contact_email')
                ->label('Contact Email'),
            ExportColumn::make('name'),
            ExportColumn::make('datetime_reported')
                ->label('Datetime Reported'),
            ExportColumn::make('datetime_action')
                ->label('Datetime Action'),
            ExportColumn::make('datetime_closed')
                ->label('Datetime Closed'),
            ExportColumn::make('due_date')
                ->label('Due Date'),
            ExportColumn::make('subject'),
            ExportColumn::make('description'),
            ExportColumn::make('department.name'),
            ExportColumn::make('priority.name'),
            ExportColumn::make('status.name'),
            ExportColumn::make('assignedTo')
                ->label('Assigned To')
                ->state(fn ($record) => $record->assignedTo?->name),
            ExportColumn::make('createdBy')
                ->label('Created By')
                ->state(fn ($record) => $record->createdBy?->name),
            ExportColumn::make('created_at')
                ->label('Created At'),
            ExportColumn::make('updated_at')
                ->label('Updated At'),
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
