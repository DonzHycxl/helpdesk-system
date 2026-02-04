<?php

namespace App\Filament\Pages;

use App\Filament\Actions\ExportPriorityVsStatusPdfAction;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class TicketsByPriorityVsStatus extends Page
{
    protected static string|null|\UnitEnum $navigationGroup = 'Report Management';

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::OutlinedDocumentText;

    protected string $view = 'filament.pages.tickets-by-priority-vs-status';

    public function exportPdf(): Action
    {
        return ExportPriorityVsStatusPdfAction::make();
    }
}