<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class TicketInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ticket Details')
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->columns()
                    ->schema([
                        TextEntry::make('contact_email')
                            ->label('Contact Email'),
                        TextEntry::make('assignedTo.name')
                            ->label('Assigned To')
                            ->placeholder('Unassigned'),

                        TextEntry::make('id')
                            ->label('Ticket ID'),
                        TextEntry::make('createdBy.name')
                            ->label('Created By'),

                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d/m/Y' . str_repeat("\u{00A0}", 5) . 'h:i A'),

                        TextEntry::make('datetime_reported')
                            ->label('Datetime Reported')
                            ->dateTime('d/m/Y' . str_repeat("\u{00A0}", 5) . 'h:i A'),
                        TextEntry::make('due_date')
                            ->label('Due Date')
                            ->dateTime('d/m/Y' . str_repeat("\u{00A0}", 5) . 'h:i A'),

                        TextEntry::make('datetime_action')
                            ->label('Datetime Action')
                            ->placeholder('Unavailable')
                            ->dateTime('d/m/Y' . str_repeat("\u{00A0}", 5) . 'h:i A'),
                        TextEntry::make('department.name')
                            ->label('Department'),

                        TextEntry::make('datetime_closed')
                            ->label('Datetime Closed')
                            ->placeholder('Unavailable')
                            ->dateTime('d/m/Y' . str_repeat("\u{00A0}", 5) . 'h:i A'),
                    ]),
                Flex::make([
                    Section::make([
                        TextEntry::make('subject'),
                        TextEntry::make('description')
                    ])
                        ->heading('Ticket Content'),
                    Section::make([
                        TextEntry::make('priority.name')
                            ->label('Priority')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'Critical' => 'danger',
                                'Semi-critical' => 'warning',
                                'Non-critical' => 'success',
                                'General Enquiries' => 'gray',
                                'Change Request' => 'primary',
                            })
                            ->size(TextSize::Medium),
                        TextEntry::make('status.name')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match (true) {
                                str_contains($state, 'Open') => 'warning',
                                str_contains($state, 'Hold') => 'info',
                                str_contains($state, 'Closed') => 'success',
                            })
                            ->size(TextSize::Medium),
                    ])
                        ->grow(false)
                        ->inlineLabel()
                ])
                    ->columnSpanFull(),
            ]);
    }
}
