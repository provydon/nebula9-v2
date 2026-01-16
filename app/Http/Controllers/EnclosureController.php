<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnclosureRequest;
use App\Http\Requests\UpdateEnclosureRequest;
use App\Models\Enclosure;
use App\Services\EnclosureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnclosureController extends Controller
{
    public function __construct(
        private EnclosureService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->service->getAll($request->only(['type', 'available', 'full'])));
    }

    public function show(Enclosure $enclosure): JsonResponse
    {
        return response()->json($enclosure->load(['animals'])->loadCount('animals'));
    }

    public function store(StoreEnclosureRequest $request): JsonResponse
    {
        return response()->json($this->service->create($request->validated()), 201);
    }

    public function update(UpdateEnclosureRequest $request, Enclosure $enclosure): JsonResponse
    {
        return response()->json($this->service->update($enclosure, $request->validated()));
    }

    public function destroy(Enclosure $enclosure): JsonResponse
    {
        $this->service->delete($enclosure);

        return response()->json(['message' => 'Enclosure deleted']);
    }
}
