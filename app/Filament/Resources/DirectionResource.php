<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DirectionResource\Pages;
use App\Models\Direction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas;
use Filament\Actions;
use Filament\Tables\Table;

class DirectionResource extends Resource
{
    protected static ?string $model = Direction::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bookmark';
    protected static string | \UnitEnum | null $navigationGroup = 'Test Management';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Schemas\Components\Section::make('Direction Details')->schema([
                Forms\Components\Select::make('section_id')
                    ->relationship('section', 'name')
                    ->required()
                    ->preload()
                    ->searchable(),
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('e.g. Part A, Part B, Directions')
                    ->helperText('Short label for this direction (e.g. Part A, Part B).'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Short Conversations, Longer Conversations')
                    ->helperText('Descriptive title displayed on the exam page.'),
                Forms\Components\RichEditor::make('description')
                    ->label('Direction Content / Text')
                    ->nullable()
                    ->columnSpanFull()
                    ->placeholder('Type instructions or example questions here...')
                    ->helperText('Complete instructions displayed before questions start.'),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->helperText('Display order within the section.'),
            ])->columns(2),
            Schemas\Components\Section::make('Direction Audio')
                ->description('Upload audio for this direction. Audio will be playable on the exam page.')
                ->schema([
                    Forms\Components\FileUpload::make('audio_path')
                        ->label('Directions Audio')
                        ->disk('public')
                        ->directory('directions-audio')
                        ->visibility('public')
                        ->acceptedFileTypes([
                            'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg',
                            'audio/m4a', 'audio/x-m4a', 'audio/mp4', 'audio/aac',
                            'audio/webm', 'audio/flac',
                        ])
                        ->maxSize(512000)
                        ->openable()
                        ->downloadable()
                        ->nullable()
                        ->helperText('Upload audio directions file.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('section.name')
                    ->sortable()
                    ->badge()
                    ->label('Section'),
                Tables\Columns\TextColumn::make('label')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('questions_count')
                    ->counts('questions')
                    ->label('Questions')
                    ->badge()
                    ->color('info'),
                Tables\Columns\IconColumn::make('audio_path')
                    ->label('Audio')
                    ->icon(fn ($state) => $state ? 'heroicon-o-speaker-wave' : 'heroicon-o-x-mark')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
            ])
            ->defaultSort('section_id')
            ->filters([
                Tables\Filters\SelectFilter::make('section_id')
                    ->relationship('section', 'name')
                    ->label('Section'),
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
            'index' => Pages\ListDirections::route('/'),
            'create' => Pages\CreateDirection::route('/create'),
            'edit' => Pages\EditDirection::route('/{record}/edit'),
        ];
    }
}
