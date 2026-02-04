<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Actions\PreviousAction;
use App\Filament\Actions\NextAction;
use App\Filament\Resources\Tickets\TicketResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    public function getTitle(): string
    {
        return "View Ticket";
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            PreviousAction::make(),
            NextAction::make(),
        ];
    }
}
