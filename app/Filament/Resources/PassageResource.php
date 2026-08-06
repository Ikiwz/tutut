<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PassageResource\Pages;
use App\Models\Passage;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas;
use Filament\Actions;
use Filament\Tables\Table;

class PassageResource extends Resource
{
    protected static ?string $model = Passage::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected static string | \UnitEnum | null $navigationGroup = 'Test Management';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Schemas\Components\Section::make('Passage Details')->schema([
                Forms\Components\Select::make('section_id')
                    ->relationship('section', 'name')
                    ->required()
                    ->preload(),
                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                Forms\Components\TextInput::make('order')->numeric()->default(0)->required(),
                Forms\Components\RichEditor::make('content')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('audio_path')
                    ->label('Audio File')
                    ->disk('public')
                    ->directory('passage-audio')
                    ->visibility('public')
                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/m4a', 'audio/x-m4a', 'audio/mp4', 'audio/aac', 'audio/webm', 'audio/flac'])
                    ->maxSize(512000)
                    ->openable()
                    ->downloadable()
                    ->nullable(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('section.name')->badge()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPassages::route('/'),
            'create' => Pages\CreatePassage::route('/create'),
            'edit' => Pages\EditPassage::route('/{record}/edit'),
        ];
    }
}


