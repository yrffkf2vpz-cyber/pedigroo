<?php

namespace App\Http\Livewire\Dog;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Services\Dog\DogChampionshipService;

class DogChampionshipSection extends Component
{
    public int $dogId;
    public array $championships = [];
    public ?string $error = null;

    protected $listeners = [
        'refreshChampionships' => 'loadChampionships',
    ];

    public function mount(int $dogId)
    {
        // jogosultság ellenőrzés Livewire-ben
        if (!auth()->user()?->can('admin')) {
            abort(403, 'Nincs jogosultság.');
        }

        $this->dogId = $dogId;
        $this->loadChampionships();
    }

    public function loadChampionships()
    {
        try {
            $service = app(DogChampionshipService::class);
            $this->championships = $service->getForDog($this->dogId);

            Log::info('Livewire: Dog championships loaded', [
                'user_id' => auth()->id(),
                'dog_id'  => $this->dogId,
            ]);

        } catch (\Throwable $e) {

            Log::error('Livewire: Dog championships load failed', [
                'user_id' => auth()->id(),
                'dog_id'  => $this->dogId,
                'error'   => $e->getMessage(),
            ]);

            $this->error = 'A championship adatok betöltése sikertelen.';
            $this->championships = [];
        }
    }

    public function render()
    {
        return view('livewire.dog.championship-section');
    }
}
