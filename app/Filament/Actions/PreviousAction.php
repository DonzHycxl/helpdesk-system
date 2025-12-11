<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class PreviousAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'previous';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->hiddenLabel()
            ->icon('heroicon-o-arrow-left')
            ->outlined()
            ->tooltip(fn (): ?string => $this->getUrl() !== null ? "Previous Ticket" : null);

        $this->url(function (): ?string {
            $livewire = $this->getLivewire();

            if (! $livewire instanceof ViewRecord) {
                return null;
            }

            $record = $livewire->getRecord();
            $resource = $livewire::getResource();

            $previousRecord = $resource::getModel()::query()
                ->where('id', '<', $record->id)
                ->orderBy('id', 'desc')
                ->first();

            if (! $previousRecord) {
                return null;
            }

            return $resource::getUrl('view', ['record' => $previousRecord]);
        });

        $this->disabled(fn (): bool => $this->getUrl() === null)
            ->color(fn (): string => $this->getUrl() === null ? 'gray' : 'primary');
    }
}
