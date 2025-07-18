<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkTimeResource\Pages;
use App\Filament\Resources\WorkTimeResource\RelationManagers;
use App\Models\WorkTime;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Carbon;

class WorkTimeResource extends Resource
{
    protected static ?string $model = WorkTime::class;
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('task_id')
                ->label('Task')
                ->relationship('task', 'title')
                ->required(),

            DatePicker::make('work_date')
                ->label('Work Date')
                ->required()
                ->jalali(),

            TimePicker::make('start_time')
                ->label('Start Time')
                ->required(),

            TimePicker::make('end_time')
                ->label('End Time')
                ->required(),

            Textarea::make('description')
                ->label('Description')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('task.title')
                    ->label('Task')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('work_date')
                    ->label('Work Date')
                    ->jalaliDate('Y/m/d'),

                TextColumn::make('start_time')
                    ->label('Start'),

                TextColumn::make('end_time')
                    ->label('End'),

                TextColumn::make('time')
                    ->label('Time')
                    ->getStateUsing(function ($record) {
                        $totalMinutes = 0;
                        if ($record->start_time && $record->end_time) {
                            $totalMinutes = Carbon::parse($record->start_time)->diffInMinutes(Carbon::parse($record->end_time));
                        }
                        $hours = floor($totalMinutes / 60);
                        $minutes = $totalMinutes % 60;

                        return sprintf('%02d:%02d', $hours, $minutes);
                    })
                    ->html(true),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(30),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkTimes::route('/'),
            'create' => Pages\CreateWorkTime::route('/create'),
            'edit' => Pages\EditWorkTime::route('/{record}/edit'),
        ];
    }
}
