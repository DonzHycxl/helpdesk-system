<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Details')
                    ->columnSpanFull()
                    ->columns()
                    ->extraAttributes(['class' => 'user-details-section'])
                    ->schema([
                        TextEntry::make('name')
                            ->icon(Heroicon::UserCircle)
                            ->iconColor('primary')
                            ->size(TextSize::Medium),
                        TextEntry::make('username')
                            ->icon(Heroicon::User)
                            ->iconColor('primary')
                            ->size(TextSize::Medium),
                        TextEntry::make('email')
                            ->icon(Heroicon::Envelope)
                            ->iconColor('primary')
                            ->label('Email address')
                            ->size(TextSize::Medium),
                        IconEntry::make('is_admin')
                            ->boolean()
                            ->size(IconSize::Large),
                        TextEntry::make('created_at')
                            ->icon(Heroicon::CalendarDays)
                            ->iconColor('primary')
                            ->dateTime('d/m/Y' . str_repeat("\u{00A0}", 5) . 'h:i A')
                            ->placeholder('-')
                            ->size(TextSize::Medium),
                        TextEntry::make('updated_at')
                            ->icon(Heroicon::CalendarDays)
                            ->iconColor('primary')
                            ->dateTime('d/m/Y' . str_repeat("\u{00A0}", 5) . 'h:i A')
                            ->placeholder('-')
                            ->size(TextSize::Medium),
                    ]),
            ]);
    }
}
