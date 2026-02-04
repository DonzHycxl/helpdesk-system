<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class NextAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'next';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->hiddenLabel()
            ->icon('heroicon-o-arrow-right')
            ->outlined()
            ->tooltip(fn (): ?string => $this->getUrl() !== null ? "Next Ticket" : null);

        $this->url(function (): ?string {
            $livewire = $this->getLivewire();

            if (! $livewire instanceof ViewRecord) {
                return null;
            }

            $record = $livewire->getRecord();
            $resource = $livewire::getResource();

            $nextRecord = $resource::getModel()::query()
                ->where('id', '>', $record->id)
                ->orderBy('id', 'asc')
                ->first();

            if (! $nextRecord) {
                return null;
            }

            return $resource::getUrl('view', ['record' => $nextRecord]);
        });

        $this->disabled(fn (): bool => $this->getUrl() === null)
            ->color(fn (): string => $this->getUrl() === null ? 'gray' : 'primary');
    }
}
