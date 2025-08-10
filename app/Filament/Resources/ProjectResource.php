<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Carbon;
use RyanChandler\FilamentProgressColumn\ProgressColumn;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;


class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-command-line';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Grid::make(3)->schema([
                TextInput::make('title')
                    ->label('Project Title')
                    ->required()
                    ->maxLength(255),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'todo' => 'Todo',
                        'doing' => 'Doing',
                        'done' => 'Done',
                        'hold' => 'Hold',
                        'ongoing' => 'Ongoing',
                    ])
                    ->default('todo')
                    ->required(),

                Toggle::make('is_permanent')
                    ->label('Permanent Project')
                    ->inline(false)
                    ->reactive(),
            ]),

            Grid::make(3)->schema([
                TextInput::make('total_cost')
                    ->label('Total Cost')
                    ->prefix('IRR')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->visible(fn(Get $get) => !$get('is_permanent')),

                TextInput::make('hourly_rate')
                    ->label('Hourly Rate')
                    ->prefix('IRR')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->visible(fn(Get $get) => $get('is_permanent')),

                TextInput::make('estimated_hours')
                    ->label('Estimated Hours')
                    ->numeric()
                    ->visible(fn(Get $get) => !$get('is_permanent')),

                TextInput::make('real_hours')
                    ->label('Real Hours')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
            ]),

            Textarea::make('description')
                ->label('Description')
                ->columnSpanFull(),

            Repeater::make('tasks')
                ->label('Tasks')
                ->relationship('tasks')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->placeholder('Enter task description...'),
                    ]),

                    Grid::make(2)->schema([
                        DatePicker::make('start_date')
                            ->label('Start Date'),

                        DatePicker::make('due_date')
                            ->label('Due Date'),
                    ]),
                ])
                ->defaultItems(0)
                ->reorderableWithButtons()
                ->collapsible()
                ->itemLabel(fn(array $state): ?string => $state['title'] ?? null)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Project::query()->with('workTimes'))
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('is_permanent')
                    ->label('Permanent')
                    ->badge()
                    ->color(fn(bool $state) => $state ? 'info' : 'secondary')
                    ->formatStateUsing(fn(bool $state) => $state ? 'Yes' : 'No'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'todo' => 'gray',
                        'doing' => 'warning',
                        'done' => 'success',
                        'hold' => 'danger',
                        'ongoing' => 'info',
                    }),

                TextColumn::make('real_hours')
                    ->label('Real Hours')
                    ->getStateUsing(function ($record) {
                        $hours = floor($record->real_hours);
                        $minutes = ($record->real_hours - $hours) * 60;
                        return sprintf('%02d:%02d', $hours, $minutes);
                    }),

                TextColumn::make('estimated_hours')
                    ->label('Est. Hours')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        $hours = floor($record->estimated_hours);
                        $minutes = ($record->estimated_hours - $hours) * 60;
                        return sprintf('%02d:%02d', $hours, $minutes);
                    }),

                TextColumn::make('work_time_sum')
                    ->label('Total Work Time')
                    ->getStateUsing(function ($record) {
                        $record->loadMissing('workTimes');
                        $totalMinutes = $record->workTimes->sum(function ($wt) {
                            if ($wt->start_time && $wt->end_time) {
                                return Carbon::parse($wt->start_time)->diffInMinutes(Carbon::parse($wt->end_time));
                            }
                            return 0;
                        });
                        $hours = floor($totalMinutes / 60);
                        $minutes = $totalMinutes % 60;

                        return sprintf('%02d:%02d', $hours, $minutes);
                    }),

                ProgressColumn::make('remaining_time')
                    ->label('Remaining Time')
                    ->progress(function ($record) {
                        if ($record->is_permanent || $record->estimated_hours === null) {
                            return 0;
                        }

                        $totalMinutes = $record->workTimes->sum(function ($wt) {
                            if ($wt->start_time && $wt->end_time) {
                                return Carbon::parse($wt->start_time)->diffInMinutes(Carbon::parse($wt->end_time));
                            }
                            return 0;
                        });
                        $estimatedMinutes = $record->estimated_hours * 60;
                        return $estimatedMinutes > 0
                            ? round(($totalMinutes / $estimatedMinutes) * 100)
                            : 0;
                    })
                    ->color(function ($record) {
                        if ($record->is_permanent || $record->estimated_hours === null) {
                            return 'secondary';
                        }

                        $totalMinutes = $record->workTimes->sum(function ($wt) {
                            if ($wt->start_time && $wt->end_time) {
                                return Carbon::parse($wt->start_time)->diffInMinutes(Carbon::parse($wt->end_time));
                            }
                            return 0;
                        });
                        $estimatedMinutes = $record->estimated_hours * 60;
                        $remainingMinutes = $estimatedMinutes - $totalMinutes;
                        $tenthOfEstimated = $estimatedMinutes * 0.1;
                        if ($remainingMinutes > 0
                            && $remainingMinutes < $tenthOfEstimated
                            && $record->status !== 'done') {
                            return 'danger';
                        }
                        return $totalMinutes >= $estimatedMinutes ? 'success' : 'warning';
                    }),


                TextColumn::make('payment_summary')
                    ->label('Payment Summary')
                    ->getStateUsing(function ($record) {
                        $record->loadMissing(['payments', 'workTimes']);

                        $formattedPaid = number_format($record->total_paid, 0, '.', ',');

                        $targetCost = $record->is_permanent
                            ? $record->calculated_total_cost
                            : $record->total_cost;

                        $formattedTarget = number_format($targetCost, 0, '.', ',');

                        return "{$formattedPaid} IRR of<br> {$formattedTarget} IRR";
                    })
                    ->html(true),

                ProgressColumn::make('payment_progress')
                    ->label('Payment Progress')
                    ->progress(function ($record) {
                        $targetCost = $record->is_permanent
                            ? $record->calculated_total_cost
                            : $record->total_cost;

                        return $targetCost > 0
                            ? round(($record->total_paid / $targetCost) * 100)
                            : 0;
                    })
                    ->color(fn($record) => $record->total_paid >= (
                    $record->is_permanent
                        ? $record->calculated_total_cost
                        : $record->total_cost
                    ) ? 'success' : 'warning'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('exportTasks')
                    ->label('Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn($record) => route('projects.exportTasks', $record->id))
                    ->openUrlInNewTab(),
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
//            \App\Filament\Resources\ProjectResource\RelationManagers\TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
