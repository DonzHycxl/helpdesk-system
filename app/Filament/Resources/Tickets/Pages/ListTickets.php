<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use App\Filament\Resources\Tickets\Widgets\TicketsStats;
use App\Models\Ticket;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TicketsStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Tickets')
                ->icon('heroicon-o-ticket')
                ->badge(fn () => Ticket::count()),

            'my_tickets' => Tab::make('My Tickets')
                ->icon('heroicon-o-user')
                ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('assigned_to_user_id', auth()->id())
                )
                ->badge(fn () => Ticket::where('assigned_to_user_id', auth()->id())->count()),

            'unassigned' => Tab::make('Unassigned')
                ->icon('heroicon-o-user-minus')
                ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('assigned_to_user_id', null)
                )
                ->badge(fn () => Ticket::where('assigned_to_user_id', null)->count()),

            'open' => Tab::make('Open')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) =>
                $query->whereHas('status', fn ($q) =>
                $q->where('name', 'Open (Unresolved)')
                )
                )
                ->badge(fn () => Ticket::whereHas('status', fn ($q) =>
                $q->where('name', 'Open (Unresolved)')
                )->count()),

            'hold' => Tab::make('Hold')
                ->icon('heroicon-o-pause-circle')
                ->modifyQueryUsing(fn (Builder $query) =>
                $query->whereHas('status', fn ($q) =>
                $q->where('name', 'Hold (In Progress)')
                )
                )
                ->badge(fn () => Ticket::whereHas('status', fn ($q) =>
                $q->where('name', 'Hold (In Progress)')
                )->count()),

            'closed' => Tab::make('Closed')
                ->icon('heroicon-o-check-badge')
                ->modifyQueryUsing(fn (Builder $query) =>
                $query->whereHas('status', fn ($q) =>
                $q->where('name', 'Closed (Resolved)')
                )
                )
                ->badge(fn () => Ticket::whereHas('status', fn ($q) =>
                $q->where('name', 'Closed (Resolved)')
                )->count()),
        ];
    }
}
