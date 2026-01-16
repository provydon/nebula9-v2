<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnimalRequest;
use App\Http\Requests\TransferAnimalRequest;
use App\Http\Requests\UpdateAnimalRequest;
use App\Models\Animal;
use App\Services\AnimalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnimalController extends Controller
{
    public function __construct(
        private AnimalService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->service->getAll($request->only(['specie', 'preferred_environment', 'enclosure_id'])));
    }

    public function show(Animal $animal): JsonResponse
    {
        return response()->json($animal->load('enclosure'));
    }

    public function store(StoreAnimalRequest $request): JsonResponse
    {
        return response()->json($this->service->create($request->validated()), 201);
    }

    public function update(UpdateAnimalRequest $request, Animal $animal): JsonResponse
    {
        return response()->json($this->service->update($animal, $request->validated()));
    }

    public function destroy(Animal $animal): JsonResponse
    {
        $this->service->delete($animal);

        return response()->json(['message' => 'Animal deleted']);
    }

    public function transfer(TransferAnimalRequest $request, Animal $animal): JsonResponse
    {
        return response()->json($this->service->transfer($animal, $request->validated()['target_enclosure_id']));
    }
}
