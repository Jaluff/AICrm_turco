<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduledMessageResource\Pages;
use App\Models\ScheduledMessage;
use App\Models\Contact;
use App\Models\ChannelConnection;
use App\Models\MessageTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ScheduledMessageResource extends Resource
{
    protected static ?string $model = ScheduledMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Mensajes Programados';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?int $navigationSort = 40;
    protected static ?string $modelLabel = 'Mensaje Programado';
    protected static ?string $pluralModelLabel = 'Mensajes Programados';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Programación')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('contact_id')
                            ->label('Destinatario (Contacto)')
                            ->relationship('contact', 'name')
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('channel_connection_id')
                            ->label('Canal')
                            ->relationship('channelConnection', 'name')
                            ->required()
                            ->searchable(),

                        Forms\Components\DateTimePicker::make('send_at')
                            ->label('Fecha y Hora de Envío')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->after('now'),

                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'sent' => 'Enviado',
                                'failed' => 'Fallido',
                                'cancelled' => 'Cancelado',
                            ])
                            ->default('pending')
                            ->required()
                            ->disabled(fn (?ScheduledMessage $record) => $record === null),
                    ]),

                Forms\Components\Section::make('Contenido')
                    ->schema([
                        Forms\Components\Select::make('message_template_id')
                            ->label('Plantilla de WhatsApp (Opcional)')
                            ->options(MessageTemplate::pluck('name', 'id'))
                            ->searchable()
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $state ? $set('body', null) : null),

                        Forms\Components\Textarea::make('body')
                            ->label('Mensaje de texto libre')
                            ->helperText('Escribe el cuerpo del mensaje si no utilizas plantilla.')
                            ->visible(fn (callable $get) => !$get('message_template_id'))
                            ->required(fn (callable $get) => !$get('message_template_id'))
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('variables')
                            ->label('Variables de Plantilla (JSON)')
                            ->helperText('Ingresa los valores ordenados. Ej: ["Juan", "1234"]')
                            ->visible(fn (callable $get) => (bool) $get('message_template_id'))
                            ->afterStateHydrated(fn ($component, $state) => $component->state(is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state))
                            ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Resultado del Envío')
                    ->visible(fn (?ScheduledMessage $record) => $record !== null && $record->status !== 'pending')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('sent_message_id')
                            ->label('Mensaje Enviado')
                            ->relationship('sentMessage', 'id')
                            ->disabled(),

                        Forms\Components\Textarea::make('error')
                            ->label('Detalles del Error')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Contacto')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('channelConnection.name')
                    ->label('Canal')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('send_at')
                    ->label('Programado Para')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'sent' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('error')
                    ->label('Error')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->error)
                    ->color('danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'sent' => 'Enviado',
                        'failed' => 'Fallido',
                        'cancelled' => 'Cancelado',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update(['status' => 'cancelled'])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('send_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScheduledMessages::route('/'),
            'create' => Pages\CreateScheduledMessage::route('/create'),
            'edit' => Pages\EditScheduledMessage::route('/{record}/edit'),
        ];
    }
}
