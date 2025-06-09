<?php

namespace App\Filament\Resources\WorkTimeResource\Pages;

use App\Filament\Resources\WorkTimeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkTime extends EditRecord
{
    protected static string $resource = WorkTimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
