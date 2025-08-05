<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Grid;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('project_id')
                ->label('Project')
                ->relationship('project', 'title')
                ->required(),

            TextInput::make('title')
                ->label('Title')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
                ->label('Description'),

            DatePicker::make('start_date')
                ->label('Start Date'),

            DatePicker::make('due_date')
                ->label('Due Date'),

            Repeater::make('subtasks')
                ->label('Subtasks')
                ->relationship('subtasks')
                ->schema([
                    Grid::make(2)->schema([
                        Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->rows(2)
                            ->placeholder('Enter subtask description...')
                            ->columnSpan(1),
                        
                        Forms\Components\Checkbox::make('is_completed')
                            ->label('Completed')
                            ->default(false)
                            ->columnSpan(1),
                    ]),
                ])
                ->defaultItems(0)
                ->reorderableWithButtons()
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['description'] ?? null)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('project.title')
                    ->label('Project')
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date(),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SubtasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
