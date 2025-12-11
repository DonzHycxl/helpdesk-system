<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Flex::make([
                    Section::make('Ticket Form')
                        ->columns()
                        ->schema([
                            TextInput::make('contact_email')
                                ->label('Contact Email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            DateTimePicker::make('datetime_reported')
                                ->label('Datetime Reported')
                                ->seconds(false)
                                ->required(),
                            DateTimePicker::make('datetime_action')
                                ->label('Datetime Action')
                                ->seconds(false),
                            DateTimePicker::make('datetime_closed')
                                ->label('Datetime Closed')
                                ->seconds(false),
                            DateTimePicker::make('due_date')
                                ->label('Due Date')
                                ->default(Carbon::today()->setTime(23, 59, 59))
                                ->seconds(false)
                                ->required(),
                            TextInput::make('subject')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Textarea::make('description')
                                ->required()
                                ->rows(5)
                                ->columnSpanFull()
                        ])
                        ->columnSpanFull(),
                    Section::make()
                        ->columns(1)
                        ->schema([
                            Select::make('department_id')
                                ->label('Department')
                                ->native(false)
                                ->relationship('department', 'name')
                                ->required(),
                            Select::make('priority_id')
                                ->label('Priority')
                                ->native(false)
                                ->relationship('priority', 'name')
                                ->getOptionLabelFromRecordUsing(fn($record) => "$record->id - $record->name")
                                ->default(2)
                                ->required(),
                            Select::make('status_id')
                                ->label('Status')
                                ->native(false)
                                ->relationship('status', 'name', fn(Builder $query) => $query->orderBy('id'))
                                ->default(2)
                                ->required(),
                            Select::make('assigned_to_user_id')
                                ->label('Assigned To')
                                ->native(false)
                                ->relationship('assignedTo', 'name')
                                ->placeholder('Unassigned')
                                ->default(null),
                        ])
                        ->grow(false)
                ])
                ->columnSpanFull(),
            ]);
    }
}
