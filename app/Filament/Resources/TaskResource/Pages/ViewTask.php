<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\IconEntry;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

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
                Section::make('Task Information')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title'),
                        TextEntry::make('description')
                            ->label('Description')
                            ->markdown(),
                        TextEntry::make('project.title')
                            ->label('Project'),
                        TextEntry::make('start_date')
                            ->label('Start Date')
                            ->date(),
                        TextEntry::make('due_date')
                            ->label('Due Date')
                            ->date(),
                    ])
                    ->columns(2),

                Section::make('Subtasks')
                    ->schema([
                        RepeatableEntry::make('subtasks')
                            ->schema([
                                TextEntry::make('description')
                                    ->label('Description'),
                                IconEntry::make('is_completed')
                                    ->label('Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ])
                            ->columns(2)
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }
}
