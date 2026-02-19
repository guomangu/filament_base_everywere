<?php
namespace App\Filament\Pages\Auth;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Facades\Filament;
use Filament\Events\Auth\Registered;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use App\Services\LocalizationService;

class Register extends BaseRegister
{
    public $raw_location = null;

    protected $listeners = [
        'address-selected' => 'handleAddressSelected',
    ];

    public function handleAddressSelected($payload = null)
    {
        if (!$payload) return;
        $this->data['location'] = $payload['query'];
        $this->raw_location = $payload['raw'];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                ViewField::make('location')
                    ->label('Votre quartier / Ville')
                    ->view('filament.forms.components.address-autocomplete')
                    ->required()
                    ->validationMessages([
                        'required' => 'La localisation est obligatoire.',
                    ]),
            ])
            ->statePath('data');
    }

    public function register(): ?RegistrationResponse
    {
        $data = $this->form->getState();

        if (!$this->raw_location) {
            $this->addError('data.location', 'Veuillez sélectionner une adresse précise dans la liste suggérée.');
            return null;
        }
        
        $user = $this->getUserModel()::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'location' => $data['location'],
        ]);

        // Auto-handle Circle logic with raw data
        LocalizationService::findOrCreateCircleForUser($user, $data['location'], $this->raw_location);

        event(new Registered($user));
        Filament::auth()->login($user);
        session()->regenerate();
        return app(RegistrationResponse::class);
    }
}
