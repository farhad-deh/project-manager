<?php

namespace App\Filament\Resources\SubtaskResource\Pages;

use App\Filament\Resources\SubtaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSubtask extends CreateRecord
{
    protected static string $resource = SubtaskResource::class;
}
