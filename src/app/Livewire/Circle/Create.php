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

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('The Creative Forge...'),
                Select::make('type')
                    ->options([
                        'business' => 'Business / Coworking',
                        'place' => 'Place / Location',
                        'event' => 'Event / Meetup',
                        'project' => 'Project / Squad',
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('address')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('123 Alpha Street, Tech City...'),
                Textarea::make('description')
                    ->required()
                    ->minLength(10)
                    ->rows(5)
                    ->placeholder('What are we building here?'),
                Toggle::make('is_public')
                    ->label('Public (Visible to all)')
                    ->default(true),
            ])
            ->statePath('data');
    }

    public function store()
    {
        $data = $this->form->getState();

        $circle = Circle::create([
            ...$data,
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
