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
use Filament\Forms\Components\TimePicker;
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
                ->label('Start Date')
                ->jalali(),

            DatePicker::make('due_date')
                ->label('Due Date')
                ->jalali(),

            // ----------------------
            // ساب تسک‌ها
            // ----------------------
            Repeater::make('subtasks')
                ->label('Subtasks')
                ->relationship('subtasks')
                ->schema([
                    Grid::make(2)->schema([
                        Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->rows(2)
                            ->placeholder('Enter subtask description...'),

                        Forms\Components\Checkbox::make('is_completed')
                            ->label('Completed')
                            ->default(false),
                    ]),
                ])
                ->defaultItems(0)
                ->reorderableWithButtons()
                ->collapsible()
                ->itemLabel(fn(array $state): ?string => $state['description'] ?? null)
                ->columnSpanFull(),

            // ----------------------
            // ثبت سریع Work Time
            // ----------------------
            Repeater::make('workTimes')
                ->label('Work Times')
                ->relationship('workTimes')
                ->schema([
                    Grid::make(3)->schema([
                        DatePicker::make('work_date')
                            ->label('Work Date')
                            ->required()
                            ->jalali()
                            ->default(now()),

                        TimePicker::make('start_time')
                            ->label('Start')
                            ->required(),

                        TimePicker::make('end_time')
                            ->label('End')
                            ->required(),
                    ]),
                ])
                ->defaultItems(0)
                ->reorderable(false)
                ->collapsible()
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
                    ->sortable()
                    ->jalaliDate('Y/m/d'),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->sortable()
                    ->jalaliDate('Y/m/d'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //RelationManagers\SubtasksRelationManager::class,
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
