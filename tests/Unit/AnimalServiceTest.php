<?php

use App\Models\Animal;
use App\Models\Enclosure;
use App\Services\AnimalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('creates an animal', function () {
    $service = new AnimalService;

    $animal = $service->create([
        'name' => 'Lion',
        'specie' => 'Panthera leo',
        'preferred_environment' => 'Savannah',
    ]);

    expect($animal)->toBeInstanceOf(Animal::class)
        ->and($animal->name)->toBe('Lion')
        ->and($animal->specie)->toBe('Panthera leo')
        ->and($animal->preferred_environment)->toBe('Savannah');
});

test('creates animal with valid enclosure', function () {
    $enclosure = Enclosure::factory()->create(['type' => 'Savannah', 'capacity' => 10]);
    $service = new AnimalService;

    $animal = $service->create([
        'name' => 'Lion',
        'specie' => 'Panthera leo',
        'preferred_environment' => 'Savannah',
        'enclosure_id' => $enclosure->id,
    ]);

    expect($animal->enclosure_id)->toBe($enclosure->id);
});

test('rejects animal when environment does not match enclosure type', function () {
    $enclosure = Enclosure::factory()->create(['type' => 'Savannah', 'capacity' => 10]);
    $service = new AnimalService;

    expect(fn () => $service->create([
        'name' => 'Penguin',
        'specie' => 'Spheniscidae',
        'preferred_environment' => 'Aquatic',
        'enclosure_id' => $enclosure->id,
    ]))->toThrow(ValidationException::class);
});

test('rejects animal when enclosure is full', function () {
    $enclosure = Enclosure::factory()->create(['type' => 'Savannah', 'capacity' => 1]);
    Animal::factory()->create(['enclosure_id' => $enclosure->id]);
    $service = new AnimalService;

    expect(fn () => $service->create([
        'name' => 'Lion',
        'specie' => 'Panthera leo',
        'preferred_environment' => 'Savannah',
        'enclosure_id' => $enclosure->id,
    ]))->toThrow(ValidationException::class);
});

test('transfers animal to valid enclosure', function () {
    $sourceEnclosure = Enclosure::factory()->create(['type' => 'Savannah', 'capacity' => 10]);
    $targetEnclosure = Enclosure::factory()->create(['type' => 'Savannah', 'capacity' => 10]);
    $animal = Animal::factory()->create([
        'preferred_environment' => 'Savannah',
        'enclosure_id' => $sourceEnclosure->id,
    ]);
    $service = new AnimalService;

    $transferred = $service->transfer($animal->id, $targetEnclosure->id);

    expect($transferred->enclosure_id)->toBe($targetEnclosure->id);
});

test('rejects transfer when environment does not match', function () {
    $sourceEnclosure = Enclosure::factory()->create(['type' => 'Savannah', 'capacity' => 10]);
    $targetEnclosure = Enclosure::factory()->create(['type' => 'Forest', 'capacity' => 10]);
    $animal = Animal::factory()->create([
        'preferred_environment' => 'Savannah',
        'enclosure_id' => $sourceEnclosure->id,
    ]);
    $service = new AnimalService;

    expect(fn () => $service->transfer($animal->id, $targetEnclosure->id))
        ->toThrow(ValidationException::class);
});
