<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Departamentos';
    protected static ?string $modelLabel = 'Departamento';
    protected static ?string $pluralModelLabel = 'Departamentos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\ColorPicker::make('color')
                            ->label('Color'),
                    ])->columns(2),

                Forms\Components\Section::make('Mensajes Automáticos')
                    ->schema([
                        Forms\Components\Textarea::make('greeting_message')
                            ->label('Mensaje de bienvenida')
                            ->rows(3),
                        Forms\Components\Textarea::make('farewell_message')
                            ->label('Mensaje de despedida')
                            ->rows(3),
                    ])->columns(1),

                Forms\Components\Section::make('Horarios de Atención')
                    ->description('Configura los horarios específicos para este departamento.')
                    ->schema([
                        Forms\Components\Toggle::make('business_hours_enabled')
                            ->label('Habilitar Horarios de Atención')
                            ->reactive(),

                        Forms\Components\Toggle::make('use_company_business_hours')
                            ->label('Usar horarios globales de la empresa')
                            ->default(true)
                            ->visible(fn (callable $get) => $get('business_hours_enabled'))
                            ->reactive(),

                        Forms\Components\Textarea::make('away_message')
                            ->label('Mensaje de Ausencia')
                            ->helperText('Se enviará automáticamente si se recibe un mensaje fuera de horario.')
                            ->visible(fn (callable $get) => $get('business_hours_enabled'))
                            ->rows(3),

                        Forms\Components\Repeater::make('businessHours')
                            ->relationship('businessHours')
                            ->label('Horarios del Departamento')
                            ->visible(fn (callable $get) => $get('business_hours_enabled') && !$get('use_company_business_hours'))
                            ->schema([
                                Forms\Components\Select::make('day_of_week')
                                    ->label('Día')
                                    ->options([
                                        1 => 'Lunes',
                                        2 => 'Martes',
                                        3 => 'Miércoles',
                                        4 => 'Jueves',
                                        5 => 'Viernes',
                                        6 => 'Sábado',
                                        7 => 'Domingo',
                                    ])
                                    ->required(),
                                
                                Forms\Components\Toggle::make('enabled')
                                    ->label('Abierto')
                                    ->default(true),

                                Forms\Components\TimePicker::make('start_time')
                                    ->label('Hora Apertura')
                                    ->seconds(false)
                                    ->required(fn (callable $get) => $get('enabled')),

                                Forms\Components\TimePicker::make('end_time')
                                    ->label('Hora Cierre')
                                    ->seconds(false)
                                    ->required(fn (callable $get) => $get('enabled')),
                            ])
                            ->columns(4)
                            ->grid(1)
                    ])->columns(1),

                Forms\Components\Section::make('Configuración de Asignación e IA')
                    ->schema([
                        Forms\Components\Toggle::make('auto_assignment_enabled')
                            ->label('Asignación automática')
                            ->required()
                            ->default(false),
                        Forms\Components\Toggle::make('assign_offline_enabled')
                            ->label('Asignar si está offline')
                            ->required()
                            ->default(false),
                        Forms\Components\Toggle::make('redistribute_unavailable_enabled')
                            ->label('Redistribuir si no está disponible')
                            ->required()
                            ->default(false),
                        Forms\Components\Toggle::make('ai_enabled')
                            ->label('IA habilitada')
                            ->required()
                            ->default(false),
                    ])->columns(2),
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
                Tables\Columns\ColorColumn::make('color')
                    ->label('Color')
                    ->sortable(),
                Tables\Columns\IconColumn::make('auto_assignment_enabled')
                    ->boolean()
                    ->label('Asig. Automática')
                    ->sortable(),
                Tables\Columns\IconColumn::make('ai_enabled')
                    ->boolean()
                    ->label('IA Habilitada')
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
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
