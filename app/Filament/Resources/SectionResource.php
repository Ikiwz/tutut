<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectionResource\Pages;
use App\Models\Section;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas;
use Filament\Actions;
use Filament\Tables\Table;

class SectionResource extends Resource
{
    protected static ?string $model = Section::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    protected static string | \UnitEnum | null $navigationGroup = 'Test Management';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Schemas\Components\Section::make('Section Details')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->rows(3),
                Forms\Components\TextInput::make('duration_minutes')
                    ->required()
                    ->numeric()
                    ->default(35)
                    ->suffix('minutes'),
                Forms\Components\TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->badge(),
                Tables\Columns\TextColumn::make('duration_minutes')->suffix(' min')->sortable(),
                Tables\Columns\TextColumn::make('directions_count')->counts('directions')->label('Directions')->badge()->color('info'),
                Tables\Columns\TextColumn::make('questions_count')->counts('questions')->label('Questions'),
                Tables\Columns\TextColumn::make('order')->sortable(),
            ])
            ->defaultSort('order')
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSections::route('/'),
            'create' => Pages\CreateSection::route('/create'),
            'edit' => Pages\EditSection::route('/{record}/edit'),
        ];
    }
}


