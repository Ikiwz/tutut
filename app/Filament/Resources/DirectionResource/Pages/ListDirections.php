<?php
namespace App\Filament\Resources\DirectionResource\Pages;
use App\Filament\Resources\DirectionResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
class ListDirections extends ListRecords
{
    protected static string $resource = DirectionResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
