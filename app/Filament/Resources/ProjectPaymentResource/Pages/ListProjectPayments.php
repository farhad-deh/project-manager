<?php

namespace App\Filament\Resources\ProjectPaymentResource\Pages;

use App\Filament\Resources\ProjectPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectPayments extends ListRecords
{
    protected static string $resource = ProjectPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
