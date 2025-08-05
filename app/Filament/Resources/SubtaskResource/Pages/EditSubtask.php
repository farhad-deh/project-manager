<?php

namespace App\Filament\Resources\SubtaskResource\Pages;

use App\Filament\Resources\SubtaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubtask extends EditRecord
{
    protected static string $resource = SubtaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
