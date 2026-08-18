<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Models\Question;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas;
use Filament\Actions;
use Filament\Tables\Table;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static string | \UnitEnum | null $navigationGroup = 'Test Management';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Schemas\Components\Section::make('Question Details')->schema([
                Forms\Components\Select::make('section_id')
                    ->relationship('section', 'name')
                    ->required()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(fn (Set $set) => $set('direction_id', null)),
                Forms\Components\Select::make('direction_id')
                    ->relationship(
                        'direction',
                        'label',
                        modifyQueryUsing: fn (Get $get, $query) =>
                            $query->when($get('section_id'), fn ($q, $sectionId) =>
                                $q->where('section_id', $sectionId)
                            )
                    )
                    ->nullable()
                    ->preload()
                    ->reactive()
                    ->helperText('Pilih Direction/Part untuk soal ini. Daftar akan difilter berdasarkan Section yang dipilih.'),
                Forms\Components\Select::make('passage_id')
                    ->relationship('passage', 'title')
                    ->nullable()
                    ->preload(),
                Forms\Components\Textarea::make('question_text')
                    ->nullable()
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('For Listening section, question text is optional as questions are represented by audio.'),
            ])->columns(2),
            Schemas\Components\Section::make('Question Audio (Listening)')
                ->schema([
                    Forms\Components\FileUpload::make('audio_path')
                        ->label('Question Audio')
                        ->disk('public')
                        ->directory('question-audio')
                        ->visibility('public')
                        ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/m4a', 'audio/x-m4a', 'audio/mp4', 'audio/aac', 'audio/webm', 'audio/flac'])
                        ->maxSize(512000)
                        ->openable()
                        ->downloadable()
                        ->nullable()
                        ->helperText('Upload audio file for listening questions. This audio will appear as a speaker icon on the exam page.'),
                ])
                ->description('Upload question audio for Listening section.'),
            Schemas\Components\Section::make('Answer Options')->schema([
                Forms\Components\Textarea::make('option_a')->required()->label('Option A')->rows(2),
                Forms\Components\FileUpload::make('option_a_audio')
                    ->label('Option A Audio')
                    ->disk('public')
                    ->directory('question-audio')
                    ->visibility('public')
                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/m4a', 'audio/x-m4a', 'audio/mp4', 'audio/aac', 'audio/webm', 'audio/flac'])
                    ->maxSize(512000)
                    ->openable()
                    ->downloadable()
                    ->nullable(),
                
                Forms\Components\Textarea::make('option_b')->required()->label('Option B')->rows(2),
                Forms\Components\FileUpload::make('option_b_audio')
                    ->label('Option B Audio')
                    ->disk('public')
                    ->directory('question-audio')
                    ->visibility('public')
                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/m4a', 'audio/x-m4a', 'audio/mp4', 'audio/aac', 'audio/webm', 'audio/flac'])
                    ->maxSize(512000)
                    ->openable()
                    ->downloadable()
                    ->nullable(),
                
                Forms\Components\Textarea::make('option_c')->required()->label('Option C')->rows(2),
                Forms\Components\FileUpload::make('option_c_audio')
                    ->label('Option C Audio')
                    ->disk('public')
                    ->directory('question-audio')
                    ->visibility('public')
                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/m4a', 'audio/x-m4a', 'audio/mp4', 'audio/aac', 'audio/webm', 'audio/flac'])
                    ->maxSize(512000)
                    ->openable()
                    ->downloadable()
                    ->nullable(),
                
                Forms\Components\Textarea::make('option_d')->required()->label('Option D')->rows(2),
                Forms\Components\FileUpload::make('option_d_audio')
                    ->label('Option D Audio')
                    ->disk('public')
                    ->directory('question-audio')
                    ->visibility('public')
                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/m4a', 'audio/x-m4a', 'audio/mp4', 'audio/aac', 'audio/webm', 'audio/flac'])
                    ->maxSize(512000)
                    ->openable()
                    ->downloadable()
                    ->nullable(),
                Forms\Components\Select::make('correct_answer')
                    ->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'])
                    ->required(),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('section.name')->sortable()->badge(),
                Tables\Columns\TextColumn::make('question_text')->limit(60)->searchable(),
                Tables\Columns\TextColumn::make('correct_answer')->badge()->color('success'),
                Tables\Columns\TextColumn::make('order')->sortable(),
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}


