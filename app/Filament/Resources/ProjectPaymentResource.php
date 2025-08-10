<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectPaymentResource\Pages;
use App\Models\ProjectPayment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

class ProjectPaymentResource extends Resource
{
    protected static ?string $model = ProjectPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('project_id')
                    ->label('Project')
                    ->relationship('project', 'title')
                    ->required(),

                TextInput::make('amount')
                    ->label('Amount')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->required(),

                DatePicker::make('paid_at')
                    ->label('Payment Date')
                    ->required()
                    ->jalali(),

                TextInput::make('description')
                    ->label('Description')
                    ->maxLength(255)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.title')
                    ->label('Project'),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('IRR'),
                TextColumn::make('paid_at')
                    ->label('Paid at')
                    ->sortable()
                    ->jalaliDate('Y/m/d'),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(30),
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
            'index' => Pages\ListProjectPayments::route('/'),
            'create' => Pages\CreateProjectPayment::route('/create'),
            'edit' => Pages\EditProjectPayment::route('/{record}/edit'),
        ];
    }
}
