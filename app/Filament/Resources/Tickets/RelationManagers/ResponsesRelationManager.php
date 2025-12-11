<?php

namespace App\Filament\Resources\Tickets\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $recordTitleAttribute = 'response';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Textarea::make('response')
                    ->rows(4)
                    ->columnSpanFull()
                    ->maxLength(65535)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Columns\TextColumn::make('user.name')
                    ->label('Sent By')
                    ->searchable()
                    ->sortable(),
                Columns\TextColumn::make('response')
                    ->limit(50)
                    ->wrap()
                    ->searchable(),
                Columns\TextColumn::make('created_at')
                    ->label('Sent At')
                    ->dateTime('d/m/Y' . str_repeat("\u{00A0}", 5) . 'h:i A')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    })
                    ->createAnother(false)
                    ->modalHeading('Send Response')
                    ->modalSubmitActionLabel('Send')
            ])
            ->recordActions([
                Actions\ViewAction::make()
                    ->modalHeading('View response')
                    ->schema([
                        Flex::make([
                            Section::make([
                                TextEntry::make('response')
                                    ->hiddenLabel(),
                            ]),
                            Section::make([
                                TextEntry::make('user.name')
                                    ->hiddenLabel(),
                                TextEntry::make('created_at')
                                    ->hiddenLabel()
                                    ->dateTime('d/m/Y h:i A'),
                            ])
                            ->grow(false)
                        ])
                    ]),
                Actions\EditAction::make()
                    ->modalHeading(__('Edit response'))
                    ->visible(fn (Model $record): bool => $record->user_id === auth()->id()),
                Actions\DeleteAction::make()
                    ->modalHeading(__('Delete response'))
                    ->visible(fn (Model $record): bool => $record->user_id === auth()->id()),
            ])
            ->toolbarActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }
}
