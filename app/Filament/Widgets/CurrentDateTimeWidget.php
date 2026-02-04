<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Carbon\Carbon;

class CurrentDateTimeWidget extends Widget
{
    protected string $view = 'filament.widgets.current-date-time-widget';

    public function getCurrentDateTime()
    {
        return Carbon::now()->toDayDateTimeString();
    }
}
