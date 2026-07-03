<?php

namespace App\Filament\Resources\ContactReasonResource\Pages;

use App\Filament\Resources\ContactReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactReasons extends ListRecords
{
    protected static string $resource = ContactReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
