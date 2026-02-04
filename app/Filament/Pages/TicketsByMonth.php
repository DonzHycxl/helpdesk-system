<?php

namespace App\Filament\Pages;

use App\Filament\Actions\ExportPdfAction;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class TicketsByMonth extends Page
{
    protected static string|null|\UnitEnum $navigationGroup = 'Report Management';

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::OutlinedDocumentText;

    protected string $view = 'filament.pages.tickets-by-month';

    public function exportPdf(): Action
    {
        return ExportPdfAction::make();
    }
}
