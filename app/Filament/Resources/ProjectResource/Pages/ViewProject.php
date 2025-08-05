<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Project Information')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title'),
                        TextEntry::make('description')
                            ->label('Description')
                            ->markdown(),
                        IconEntry::make('is_permanent')
                            ->label('Permanent Project')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'todo' => 'gray',
                                'doing' => 'warning',
                                'done' => 'success',
                                'hold' => 'danger',
                                'ongoing' => 'info',
                            }),
                        TextEntry::make('total_cost')
                            ->label('Total Cost')
                            ->money('IRR')
                            ->visible(fn($record) => !$record->is_permanent),
                        TextEntry::make('hourly_rate')
                            ->label('Hourly Rate')
                            ->money('IRR')
                            ->visible(fn($record) => $record->is_permanent),
                        TextEntry::make('estimated_hours')
                            ->label('Estimated Hours')
                            ->visible(fn($record) => !$record->is_permanent),
                    ])
                    ->columns(2),
            ]);
    }
}
