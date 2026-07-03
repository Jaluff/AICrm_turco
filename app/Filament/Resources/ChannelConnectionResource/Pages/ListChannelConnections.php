<?php

namespace App\Filament\Resources\ChannelConnectionResource\Pages;

use App\Filament\Resources\ChannelConnectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChannelConnections extends ListRecords
{
    protected static string $resource = ChannelConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
