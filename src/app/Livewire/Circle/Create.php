<?php

namespace App\Livewire\Circle;

use App\Models\Circle;
use App\Models\CircleMember;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class Create extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public $raw_address = null;

    protected $listeners = [
        'address-selected' => 'handleAddressSelected',
    ];

    public function handleAddressSelected($payload = null)
    {
        if (!$payload) return;
        $this->data['address'] = $payload['query'];
        $this->raw_address = $payload['raw'];
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Grid::make(1)
                    ->schema([
                        TextInput::make('name')
                            ->label('Circle Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('The Creative Forge...'),
                        \Filament\Forms\Components\ViewField::make('address')
                            ->label('Location / City')
                            ->view('filament.forms.components.address-autocomplete')
                            ->required(),
                        Textarea::make('description')
                            ->label('Mission & Description')
                            ->required()
                            ->minLength(10)
                            ->rows(4)
                            ->placeholder('What are we building here?'),
                    ])
            ])
            ->statePath('data');
    }

    public function store()
    {
        $data = $this->form->getState();

        if (!$this->raw_address) {
            $this->addError('data.address', 'Veuillez sélectionner une adresse dans la liste suggérée.');
            return null;
        }

        $parsed = \App\Services\LocalizationService::parseAddress($data['address'], $this->raw_address);

        $circle = Circle::create([
            ...$data,
            'address' => $parsed['full_address'],
            'city' => $parsed['city'],
            'neighborhood' => $parsed['neighborhood'],
            'region' => $parsed['region'],
            'type' => 'project', // Default type
            'is_public' => true, // Hidden default
            'owner_id' => auth()->id(),
            'coordinates' => ['lat' => 0, 'lng' => 0], // Placeholder
        ]);

        // Automatically join as admin member
        CircleMember::create([
            'circle_id' => $circle->id,
            'user_id' => auth()->id(),
            'role' => 'admin',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        return redirect()->route('circles.show', $circle);
    }

    public function render()
    {
        return view('livewire.circle.create');
    }
}
