<?php

namespace App\Livewire\Achievement;

use App\Models\Achievement;
use App\Models\Skill;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Built the Decentralized Registry...'),
                Select::make('skill_id')
                    ->label('Primary Skill')
                    ->options(Skill::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->native(false),
                Select::make('circle_id')
                    ->label('Associate with Circle (Optional)')
                    ->options(auth()->user()->joinedCircles()->pluck('name', 'id'))
                    ->searchable()
                    ->native(false),
                Textarea::make('description')
                    ->label('Proof Details & Context')
                    ->required()
                    ->minLength(10)
                    ->rows(5)
                    ->placeholder('Quantify your impact. What exactly did you achieve?'),
            ])
            ->statePath('data');
    }

    public function store()
    {
        $data = $this->form->getState();

        $achievement = Achievement::create([
            ...$data,
            'user_id' => auth()->id(),
            'is_verified' => false,
        ]);

        return redirect()->route('users.show', auth()->user());
    }

    public function render()
    {
        return view('livewire.achievement.create');
    }
}
