<?php

namespace App\Services;

use App\Models\Enclosure;

class EnclosureService
{
    public function getAll(array $filters = [])
    {
        return Enclosure::with('animals')
            ->withCount('animals')
            ->when(isset($filters['type']), fn ($q) => $q->byType($filters['type']))
            ->when(! empty($filters['available']), fn ($q) => $q->available())
            ->when(! empty($filters['full']), fn ($q) => $q->full())
            ->get();
    }

    public function getById(int $id)
    {
        return Enclosure::with('animals')
            ->withCount('animals')
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return Enclosure::create($data);
    }

    public function update(Enclosure $enclosure, array $data)
    {
        $enclosure->update($data);

        return $enclosure->refresh();
    }

    public function delete(Enclosure $enclosure)
    {
        return $enclosure->delete();
    }
}
