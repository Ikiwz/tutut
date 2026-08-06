<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamAttemptResource\Pages;
use App\Models\ExamAttempt;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Schemas;
use Filament\Actions;
use Filament\Tables\Table;

class ExamAttemptResource extends Resource
{
    protected static ?string $model = ExamAttempt::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';
    protected static string | \UnitEnum | null $navigationGroup = 'Reports';
    protected static ?string $navigationLabel = 'Exam Results';
    protected static ?int $navigationSort = 5;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable()->sortable()->label('Student'),
                Tables\Columns\TextColumn::make('testSession.title')->sortable()->label('Test'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state) => match ($state) {
                        'completed' => 'success',
                        'in_progress' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('score_listening')->label('Listening'),
                Tables\Columns\TextColumn::make('score_structure')->label('Structure'),
                Tables\Columns\TextColumn::make('score_reading')->label('Reading'),
                Tables\Columns\TextColumn::make('total_score')->label('Total')->sortable()
                    ->badge()->color('success'),
                Tables\Columns\TextColumn::make('started_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('completed_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExamAttempts::route('/'),
        ];
    }
}


