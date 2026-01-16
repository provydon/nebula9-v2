<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnimalRequest;
use App\Http\Requests\TransferAnimalRequest;
use App\Http\Requests\UpdateAnimalRequest;
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
        return response()->json(
            $this->service->getAll($request->only(['specie', 'preferred_environment', 'enclosure_id']))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->service->getById($id));
    }

    public function store(StoreAnimalRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->create($request->validated()),
            201
        );
    }

    public function update(UpdateAnimalRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->service->update(
                $this->service->getById($id),
                $request->validated()
            )
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($this->service->getById($id));

        return response()->json(['message' => 'Animal deleted'], 200);
    }

    public function transfer(TransferAnimalRequest $request, int $animal): JsonResponse
    {
        return response()->json(
            $this->service->transfer($animal, $request->validated()['target_enclosure_id'])
        );
    }
}
