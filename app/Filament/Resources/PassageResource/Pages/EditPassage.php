<?php
namespace App\Filament\Resources\PassageResource\Pages;
use App\Filament\Resources\PassageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditPassage extends EditRecord
{
    protected static string $resource = PassageResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
