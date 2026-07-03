<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\ScheduledMessage;
use App\Models\ChannelConnection;
use App\Models\MessageTemplate;
use Filament\Notifications\Notification;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Contactos';
    protected static ?string $modelLabel = 'Contacto';
    protected static ?string $pluralModelLabel = 'Contactos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles del Contacto')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nickname')
                            ->label('Apodo')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('language')
                            ->label('Idioma')
                            ->maxLength(10)
                            ->default('es'),
                        Forms\Components\TextInput::make('avatar_url')
                            ->label('URL del Avatar')
                            ->url()
                            ->maxLength(2048),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Etiquetas')
                    ->schema([
                        Forms\Components\Toggle::make('opt_in')
                            ->label('Suscripción Activa (Opt In)')
                            ->required()
                            ->default(true),
                        Forms\Components\Toggle::make('opt_out')
                            ->label('Baja Voluntaria (Opt Out)')
                            ->required()
                            ->default(false),
                        Forms\Components\Select::make('tags')
                            ->label('Etiquetas')
                            ->multiple()
                            ->relationship('tags', 'name')
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make('Campos Personalizados')
                    ->schema([
                        Forms\Components\KeyValue::make('custom_fields')
                            ->label('Campos personalizados'),
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
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('opt_in')
                    ->label('Suscripción Activa')
                    ->boolean()
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
                Tables\Actions\Action::make('scheduleMessage')
                    ->label('Programar Mensaje')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('channel_connection_id')
                            ->label('Canal')
                            ->options(fn () => ChannelConnection::pluck('name', 'id'))
                            ->required()
                            ->searchable(),

                        Forms\Components\DateTimePicker::make('send_at')
                            ->label('Fecha y Hora de Envío')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->after('now'),

                        Forms\Components\Select::make('message_template_id')
                            ->label('Plantilla (Opcional)')
                            ->options(fn () => MessageTemplate::pluck('name', 'id'))
                            ->searchable()
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $state ? $set('body', null) : null),

                        Forms\Components\Textarea::make('body')
                            ->label('Mensaje de texto libre')
                            ->visible(fn (callable $get) => !$get('message_template_id'))
                            ->required(fn (callable $get) => !$get('message_template_id'))
                            ->rows(4),

                        Forms\Components\Textarea::make('variables')
                            ->label('Variables (JSON)')
                            ->helperText('Valores ordenados. Ej: ["Juan", "1234"]')
                            ->visible(fn (callable $get) => (bool) $get('message_template_id'))
                            ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
                            ->rows(3),
                    ])
                    ->action(function (Contact $record, array $data) {
                        ScheduledMessage::create([
                            'contact_id' => $record->id,
                            'channel_connection_id' => $data['channel_connection_id'],
                            'message_template_id' => $data['message_template_id'] ?? null,
                            'body' => $data['body'] ?? null,
                            'variables' => isset($data['variables']) ? (is_string($data['variables']) ? json_decode($data['variables'], true) : $data['variables']) : null,
                            'send_at' => $data['send_at'],
                            'status' => 'pending',
                            'created_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Mensaje programado con éxito')
                            ->success()
                            ->send();
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
