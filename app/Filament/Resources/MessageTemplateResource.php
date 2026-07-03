<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageTemplateResource\Pages;
use App\Models\MessageTemplate;
use App\Models\ChannelConnection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MessageTemplateResource extends Resource
{
    protected static ?string $model = MessageTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Plantillas';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?int $navigationSort = 35;
    protected static ?string $modelLabel = 'Plantilla';
    protected static ?string $pluralModelLabel = 'Plantillas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles de la Plantilla')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('channel_connection_id')
                            ->label('Canal Asociado')
                            ->options(ChannelConnection::pluck('name', 'id'))
                            ->required()
                            ->searchable(),

                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de la Plantilla')
                            ->helperText('Ej: bienvenida_cliente (debe coincidir con Meta)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('external_template_id')
                            ->label('ID Externo de la Plantilla')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('language')
                            ->label('Idioma')
                            ->default('es')
                            ->required()
                            ->maxLength(10),

                        Forms\Components\TextInput::make('category')
                            ->label('Categoría')
                            ->helperText('Ej: UTILITY, MARKETING')
                            ->maxLength(100),

                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'APPROVED' => 'Aprobada',
                                'PENDING' => 'Pendiente',
                                'REJECTED' => 'Rechazada',
                            ])
                            ->default('APPROVED')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Estructura y Parámetros')
                    ->schema([
                        Forms\Components\Textarea::make('components')
                            ->label('Componentes (JSON)')
                            ->helperText('Ejemplo: [{"type": "BODY", "text": "Hola {{1}}, tu código es {{2}}"}]')
                            ->afterStateHydrated(fn ($component, $state) => $component->state(is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state))
                            ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
                            ->rows(6)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('variables')
                            ->label('Variables Esperadas (JSON)')
                            ->helperText('Ejemplo: ["nombre_cliente", "codigo"]')
                            ->afterStateHydrated(fn ($component, $state) => $component->state(is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state))
                            ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('channelConnection.name')
                    ->label('Canal')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('language')
                    ->label('Idioma')
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Categoría')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'APPROVED' => 'success',
                        'PENDING' => 'warning',
                        'REJECTED' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'APPROVED' => 'Aprobada',
                        'PENDING' => 'Pendiente',
                        'REJECTED' => 'Rechazada',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMessageTemplates::route('/'),
            'create' => Pages\CreateMessageTemplate::route('/create'),
            'edit' => Pages\EditMessageTemplate::route('/{record}/edit'),
        ];
    }
}
