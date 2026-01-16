<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnclosureRequest;
use App\Http\Requests\UpdateEnclosureRequest;
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
        return response()->json(
            $this->service->getAll($request->only(['type', 'available', 'full']))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->service->getById($id));
    }

    public function store(StoreEnclosureRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->create($request->validated()),
            201
        );
    }

    public function update(UpdateEnclosureRequest $request, int $id): JsonResponse
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

        return response()->json(['message' => 'Enclosure deleted'], 200);
    }
}
