<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestSessionResource\Pages;
use App\Models\TestSession;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas;
use Filament\Actions;
use Filament\Tables\Table;

class TestSessionResource extends Resource
{
    protected static ?string $model = TestSession::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string | \UnitEnum | null $navigationGroup = 'Test Management';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Schemas\Components\Section::make('Session Details')->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                Forms\Components\Textarea::make('description')->rows(3),
                Forms\Components\Toggle::make('is_active')->default(true),
                Forms\Components\DateTimePicker::make('starts_at'),
                Forms\Components\DateTimePicker::make('ends_at'),
                Forms\Components\Select::make('sections')
                    ->relationship('sections', 'name')
                    ->multiple()
                    ->preload(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sections.name')->badge(),
                Tables\Columns\TextColumn::make('exam_attempts_count')->counts('examAttempts')->label('Attempts'),
                Tables\Columns\TextColumn::make('starts_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('ends_at')->dateTime()->sortable(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestSessions::route('/'),
            'create' => Pages\CreateTestSession::route('/create'),
            'edit' => Pages\EditTestSession::route('/{record}/edit'),
        ];
    }
}


