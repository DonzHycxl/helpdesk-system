<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('username')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Toggle::make('is_admin')
                    ->label('Administrator')
                    ->visible(fn () => auth()->user()?->is_admin ?? false)
                    ->disabled(fn ($record) => $record?->id === auth()->id())
                    ->helperText('Admins have full access to all resources including user management')
                    ->required(),
            ]);
    }
}
