<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Carbon;
use RyanChandler\FilamentProgressColumn\ProgressColumn;


class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-command-line';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->label('Title'),

            Textarea::make('description')
                ->label('Description'),

            TextInput::make('estimated_hours')
                ->numeric()
                ->label('Estimated Hours'),

            TextInput::make('total_cost')
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->numeric()
                ->label('Total Cost')
                ->prefix('IRR'),

            Select::make('status')
                ->label('Status')
                ->options([
                    'todo' => 'Todo',
                    'doing' => 'Doing',
                    'done' => 'Done',
                    'hold' => 'Hold',
                ])
                ->required()
                ->default('todo'),

            TextInput::make('real_hours')
                ->numeric()
                ->label('Real Hours')
                ->disabled()
                ->dehydrated(false),
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

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'todo' => 'gray',
                        'doing' => 'warning',
                        'done' => 'success',
                        'hold' => 'danger',
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
                        $formattedPaid = number_format($record->total_paid, 0, '.', ',');
                        $formattedCost = number_format($record->total_cost, 0, '.', ',');
                        return "{$formattedPaid} IRR of<br> {$formattedCost} IRR";
                    })
                    ->html(true),

                ProgressColumn::make('payment_progress')
                    ->label('Payment Progress')
                    ->progress(function ($record) {
                        return $record->total_cost > 0
                            ? round(($record->total_paid / $record->total_cost) * 100)
                            : 0;
                    })
                    ->color(function ($record) {
                        return $record->total_paid >= $record->total_cost ? 'success' : 'warning';
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
