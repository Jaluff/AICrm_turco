<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Company;
use App\Models\BusinessHour;
use App\Support\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;

class BusinessHoursSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Horarios de Atención';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?int $navigationSort = 38;
    protected static ?string $title = 'Horarios de Atención';

    protected static string $view = 'filament.pages.business-hours-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $company = Tenant::get();
        if (!$company && auth()->check()) {
            $company = auth()->user()->company;
            if ($company) {
                Tenant::set($company);
            }
        }

        if (!$company) {
            abort(403, 'No active tenant.');
        }

        // Asegurar que existan los 7 registros de horarios de lunes a domingo
        $this->ensureBusinessHoursExist($company);

        $company->load('businessHours');

        $this->form->fill([
            'business_hours_enabled' => $company->business_hours_enabled,
            'away_message' => $company->away_message,
            'hours' => $company->businessHours->map(fn ($h) => [
                'id' => $h->id,
                'day_of_week' => $h->day_of_week,
                'enabled' => $h->enabled,
                'start_time' => $h->start_time,
                'end_time' => $h->end_time,
            ])->sortBy('day_of_week')->values()->toArray(),
        ]);
    }

    protected function ensureBusinessHoursExist(Company $company): void
    {
        for ($day = 1; $day <= 7; $day++) {
            BusinessHour::firstOrCreate([
                'company_id' => $company->id,
                'department_id' => null,
                'day_of_week' => $day,
            ], [
                'enabled' => true,
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuración General')
                    ->description('Activa o desactiva las restricciones de horario de atención para toda la empresa.')
                    ->schema([
                        Forms\Components\Toggle::make('business_hours_enabled')
                            ->label('Habilitar Horarios de Atención')
                            ->reactive(),

                        Forms\Components\Textarea::make('away_message')
                            ->label('Mensaje de Ausencia (Fuera de Horario)')
                            ->helperText('Se enviará automáticamente a los clientes que escriban fuera de horario.')
                            ->visible(fn (callable $get) => $get('business_hours_enabled'))
                            ->required(fn (callable $get) => $get('business_hours_enabled'))
                            ->rows(3),
                    ])->columns(1),

                Forms\Components\Section::make('Horarios Semanales')
                    ->description('Define los rangos horarios de operación por día.')
                    ->visible(fn (callable $get) => $get('business_hours_enabled'))
                    ->schema([
                        Forms\Components\Repeater::make('hours')
                            ->label('Días de la semana')
                            ->addable(false)
                            ->deletable(false)
                            ->schema([
                                Forms\Components\Hidden::make('id'),
                                
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
                                    ->disabled()
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
                    ])
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $company = Tenant::get();
        if (!$company && auth()->check()) {
            $company = auth()->user()->company;
            if ($company) {
                Tenant::set($company);
            }
        }

        if (!$company) {
            return;
        }

        $formData = $this->form->getState();

        $company->update([
            'business_hours_enabled' => $formData['business_hours_enabled'],
            'away_message' => $formData['away_message'] ?? null,
        ]);

        if ($formData['business_hours_enabled'] && isset($formData['hours'])) {
            foreach ($formData['hours'] as $hourData) {
                if (isset($hourData['id'])) {
                    BusinessHour::where('id', $hourData['id'])->update([
                        'enabled' => $hourData['enabled'],
                        'start_time' => $hourData['start_time'],
                        'end_time' => $hourData['end_time'],
                    ]);
                }
            }
        }

        Notification::make()
            ->title('Configuración guardada exitosamente')
            ->success()
            ->send();
    }
}
