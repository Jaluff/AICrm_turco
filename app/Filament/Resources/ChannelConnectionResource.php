<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChannelConnectionResource\Pages;
use App\Filament\Resources\ChannelConnectionResource\RelationManagers;
use App\Models\ChannelConnection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChannelConnectionResource extends Resource
{
    protected static ?string $model = ChannelConnection::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Conexiones de canales';
    protected static ?string $modelLabel = 'Conexión';
    protected static ?string $pluralModelLabel = 'Conexiones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuración General')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('Tipo de canal')
                            ->options([
                                ChannelConnection::TYPE_WHATSAPP_CLOUD => 'WhatsApp Cloud',
                                ChannelConnection::TYPE_WEBCHAT => 'Webchat (Futuro)',
                                ChannelConnection::TYPE_INSTAGRAM => 'Instagram (Futuro)',
                                ChannelConnection::TYPE_FACEBOOK => 'Facebook (Futuro)',
                            ])
                            ->default(ChannelConnection::TYPE_WHATSAPP_CLOUD)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activo',
                                'inactive' => 'Inactivo',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('WhatsApp Cloud API Credenciales')
                    ->schema([
                        Forms\Components\TextInput::make('phone_number')
                            ->label('Número de teléfono')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('external_phone_number_id')
                            ->label('ID de Teléfono Meta (external_phone_number_id)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('external_waba_id')
                            ->label('ID de Cuenta de WhatsApp Business (external_waba_id)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('external_business_id')
                            ->label('ID de Business Manager (external_business_id)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('access_token')
                            ->label('Token de acceso permanente (access_token)')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        Forms\Components\TextInput::make('app_secret')
                            ->label('Meta App Secret')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state)),
                        Forms\Components\TextInput::make('verify_token')
                            ->label('Token de verificación de webhook (verify_token)')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Mensajería y Metadata')
                    ->schema([
                        Forms\Components\Textarea::make('greeting_message')
                            ->label('Mensaje de bienvenida')
                            ->rows(3),
                        Forms\Components\Textarea::make('farewell_message')
                            ->label('Mensaje de despedida')
                            ->rows(3),
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadata adicional (JSONB)'),
                    ])->columns(1),
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
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Número')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
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
                //
            ])
            ->actions([
                Tables\Actions\Action::make('verify')
                    ->label('Verificar Conexión')
                    ->icon('heroicon-m-check-circle')
                    ->color('info')
                    ->action(function (ChannelConnection $record) {
                        if ($record->type !== ChannelConnection::TYPE_WHATSAPP_CLOUD) {
                            \Filament\Notifications\Notification::make()
                                ->title('No soportado')
                                ->body('La verificación automática solo está soportada para WhatsApp Cloud.')
                                ->warning()
                                ->send();
                            return;
                        }

                        if (!$record->external_waba_id || !$record->access_token) {
                            \Filament\Notifications\Notification::make()
                                ->title('Faltan credenciales')
                                ->body('Por favor, asegúrate de ingresar el ID de WABA y el Token de Acceso.')
                                ->danger()
                                ->send();
                            return;
                        }

                        try {
                            $url = "https://graph.facebook.com/v20.0/{$record->external_waba_id}/subscribed_apps";
                            $response = \Illuminate\Support\Facades\Http::post($url, [
                                'access_token' => $record->access_token,
                            ]);

                            if ($response->successful() && $response->json('success') === true) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Conexión Exitosa')
                                    ->body('Tu servidor está correctamente vinculado con Meta y el webhook está activo.')
                                    ->success()
                                    ->send();
                            } else {
                                $error = $response->json('error.message') ?? 'Error desconocido de Meta';
                                \Filament\Notifications\Notification::make()
                                    ->title('Error de Validación')
                                    ->body($error)
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error del Sistema')
                                ->body('No se pudo establecer la conexión: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChannelConnections::route('/'),
            'create' => Pages\CreateChannelConnection::route('/create'),
            'edit' => Pages\EditChannelConnection::route('/{record}/edit'),
        ];
    }
}
