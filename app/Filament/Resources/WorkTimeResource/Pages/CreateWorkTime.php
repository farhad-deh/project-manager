<?php

namespace App\Filament\Resources\WorkTimeResource\Pages;

use App\Filament\Resources\WorkTimeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkTime extends CreateRecord
{
    protected static string $resource = WorkTimeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
