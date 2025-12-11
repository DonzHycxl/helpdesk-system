<?php

namespace App\Filament\Resources\Tickets\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketsStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Tickets', Ticket::count())
                ->description('All tickets in the system')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('info'),

            Stat::make('Open Tickets', Ticket::whereHas('status', function ($query) {
                $query->where('name', 'Open (Unresolved)');
            })->count())
                ->description('Tickets that require attention')
                ->descriptionIcon('heroicon-m-fire')
                ->color('warning'),

            Stat::make('Hold Tickets', Ticket::whereHas('status', function ($query) {
                $query->where('name', 'Hold (In Progress)');
            })->count())
                ->description('Tickets on  hold')
                ->descriptionIcon('heroicon-m-pause-circle')
                ->color('gray'),

            Stat::make('Closed Tickets', Ticket::whereHas('status', function ($query) {
                $query->where('name', 'Closed (Resolved)');
            })->count())
                ->description('Successfully resolved tickets')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
