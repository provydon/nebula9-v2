<?php

use App\Models\Enclosure;
use App\Services\EnclosureService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('creates an enclosure', function () {
    $service = new EnclosureService;

    $enclosure = $service->create([
        'name' => 'Savannah Enclosure',
        'type' => 'Savannah',
        'capacity' => 10,
    ]);

    expect($enclosure)->toBeInstanceOf(Enclosure::class)
        ->and($enclosure->name)->toBe('Savannah Enclosure')
        ->and($enclosure->type)->toBe('Savannah')
        ->and($enclosure->capacity)->toBe(10);
});

test('gets enclosure by id', function () {
    $enclosure = Enclosure::factory()->create();
    $service = new EnclosureService;

    $found = $service->getById($enclosure->id);

    expect($found->id)->toBe($enclosure->id);
});

test('gets all enclosures with filters', function () {
    Enclosure::factory()->create(['type' => 'Savannah']);
    Enclosure::factory()->create(['type' => 'Forest']);
    $service = new EnclosureService;

    $savannahs = $service->getAll(['type' => 'Savannah']);

    expect($savannahs)->toHaveCount(1)
        ->and($savannahs->first()->type)->toBe('Savannah');
});

test('updates an enclosure', function () {
    $enclosure = Enclosure::factory()->create(['name' => 'Old Name']);
    $service = new EnclosureService;

    $updated = $service->update($enclosure, ['name' => 'New Name']);

    expect($updated->name)->toBe('New Name');
});

test('deletes an enclosure', function () {
    $enclosure = Enclosure::factory()->create();
    $service = new EnclosureService;

    $service->delete($enclosure);

    expect(Enclosure::find($enclosure->id))->toBeNull();
});
