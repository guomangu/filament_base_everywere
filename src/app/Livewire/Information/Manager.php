<?php

namespace App\Livewire\Information;

use Livewire\Component;
use Livewire\WithFileUploads;

class Manager extends Component
{
    use WithFileUploads;

    public $model; // The model to attach info to (Circle, User, Achievement)
    public $modelId;
    public $modelType;

    public $title;
    public $label;
    public $images = [];
    public $newImage; // This will handle the temporary file upload

    public $showModal = false;
    public $showViewModal = false;
    public $selectedInfo = null;

    public function mount($model)
    {
        $this->model = $model;
        $this->modelId = $model->id;
        $this->modelType = get_class($model);
    }

    public function updatedNewImage()
    {
        $this->validate([
            'newImage' => 'image|max:5120', // 5MB max
        ]);

        $path = $this->newImage->store('informations', 'public');
        $this->images[] = '/storage/' . $path;
        $this->newImage = null;
    }

    public function removeImage($index)
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images);
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'label' => 'nullable|string',
        ]);

        $this->model->informations()->create([
            'title' => $this->title,
            'label' => $this->label,
            'images' => $this->images,
            'author_id' => auth()->id(),
        ]);

        $this->reset(['title', 'label', 'images', 'showModal']);
        $this->dispatch('information-added');
        
        // Refresh the model to show new info
        $this->model->load('informations');
    }

    public function delete($id)
    {
        $info = \App\Models\Information::find($id);
        if ($info && $info->author_id === auth()->id()) {
            $info->delete();
            $this->model->load('informations');
        }
    }

    public function viewInfo($id)
    {
        $this->selectedInfo = \App\Models\Information::find($id);
        if ($this->selectedInfo) {
            $this->showViewModal = true;
        }
    }

    public function render()
    {
        return view('livewire.information.manager');
    }
}
