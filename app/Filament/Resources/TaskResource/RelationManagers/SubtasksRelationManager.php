<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class SubtasksRelationManager extends RelationManager
{
    protected static string $relationship = 'subtasks';

    protected static ?string $recordTitleAttribute = 'description';

    public function form(Form $form): Form
    {
        return $form->schema([
            Textarea::make('description')
                ->label('Description')
                ->required()
                ->rows(2)
                ->placeholder('Enter subtask description...'),

            Checkbox::make('is_completed')
                ->label('Completed')
                ->default(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable(),

                IconColumn::make('is_completed')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => route('filament.admin.resources.subtasks.edit', $record)),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order');
    }
}
