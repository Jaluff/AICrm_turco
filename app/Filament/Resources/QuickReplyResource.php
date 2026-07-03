<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuickReplyResource\Pages;
use App\Models\Department;
use App\Models\QuickReply;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuickReplyResource extends Resource
{
    protected static ?string $model = QuickReply::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationLabel = 'Respuestas Rápidas';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?int $navigationSort = 30;
    protected static ?string $modelLabel = 'Respuesta Rápida';
    protected static ?string $pluralModelLabel = 'Respuestas Rápidas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identificación')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('shortcut')
                        ->label('Atajo')
                        ->helperText('Escribe sin el "/". Ejemplo: saludo, cierre, horario')
                        ->prefix('/')
                        ->required()
                        ->maxLength(50)
                        ->alphaDash()
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\Select::make('department_id')
                        ->label('Departamento')
                        ->options(Department::orderBy('name')->pluck('name', 'id'))
                        ->placeholder('Todos los departamentos')
                        ->searchable()
                        ->nullable(),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Activa')
                        ->default(true)
                        ->inline(false),

                    Forms\Components\Toggle::make('is_shared_with_ai')
                        ->label('Compartir con IA')
                        ->helperText('La IA puede usar esta respuesta como referencia')
                        ->default(false)
                        ->inline(false),
                ]),

            Forms\Components\Section::make('Contenido')
                ->schema([
                    Forms\Components\Textarea::make('body')
                        ->label('Cuerpo del mensaje')
                        ->helperText('Variables disponibles: {{contactName}}, {{contactNumber}}, {{contactEmail}}, {{userName}}, {{greeting}}')
                        ->required()
                        ->rows(6)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('shortcut')
                    ->label('Atajo')
                    ->prefix('/')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('body')
                    ->label('Contenido')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->body)
                    ->color('gray'),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Departamento')
                    ->placeholder('Todos')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_shared_with_ai')
                    ->label('IA')
                    ->boolean()
                    ->trueIcon('heroicon-o-cpu-chip')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Activa')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department_id')
                    ->label('Departamento')
                    ->options(Department::orderBy('name')->pluck('name', 'id'))
                    ->placeholder('Todos'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas'),

                Tables\Filters\TernaryFilter::make('is_shared_with_ai')
                    ->label('Compartidas con IA'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('shortcut');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListQuickReplies::route('/'),
            'create' => Pages\CreateQuickReply::route('/create'),
            'edit'   => Pages\EditQuickReply::route('/{record}/edit'),
        ];
    }
}
