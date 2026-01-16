<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\Enclosure;
use Illuminate\Validation\ValidationException;

class AnimalService
{
    public function getAll(array $filters = [])
    {
        return Animal::with('enclosure')
            ->when(isset($filters['specie']), fn ($q) => $q->where('specie', $filters['specie']))
            ->when(isset($filters['preferred_environment']), fn ($q) => $q->where('preferred_environment', $filters['preferred_environment']))
            ->when(isset($filters['enclosure_id']), fn ($q) => $q->where('enclosure_id', $filters['enclosure_id']))
            ->get();
    }

    public function getById(int $id)
    {
        return Animal::with('enclosure')->findOrFail($id);
    }

    public function create(array $data)
    {
        if (isset($data['enclosure_id'])) {
            $this->validatePlacement($data['enclosure_id'], $data['preferred_environment']);
        }

        return Animal::create($data);
    }

    public function update(Animal $animal, array $data)
    {
        if (isset($data['enclosure_id']) && $data['enclosure_id'] !== $animal->enclosure_id) {
            $this->validatePlacement($data['enclosure_id'], $data['preferred_environment'] ?? $animal->preferred_environment);
        }

        $animal->update($data);

        return $animal->refresh();
    }

    public function delete(Animal $animal)
    {
        return $animal->delete();
    }

    public function transfer(int $animalId, int $targetEnclosureId)
    {
        $animal = $this->getById($animalId);
        $this->validatePlacement($targetEnclosureId, $animal->preferred_environment);

        $animal->update(['enclosure_id' => $targetEnclosureId]);

        return $animal->refresh();
    }

    protected function validatePlacement(int $enclosureId, string $preferredEnvironment)
    {
        $enclosure = Enclosure::withCount('animals')->findOrFail($enclosureId);

        // Rule #1: Environment must match
        if ($enclosure->type !== $preferredEnvironment) {
            throw ValidationException::withMessages([
                'enclosure_id' => "Animal's preferred environment ({$preferredEnvironment}) does not match enclosure type ({$enclosure->type})",
            ]);
        }

        // Rule #2: Enclosure must have space
        if ($enclosure->is_full) {
            throw ValidationException::withMessages([
                'enclosure_id' => "Enclosure is at maximum capacity ({$enclosure->capacity})",
            ]);
        }
    }
}
