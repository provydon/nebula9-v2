<?php

use App\Models\Animal;
use App\Models\Enclosure;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list animals', function () {
    Animal::factory()->count(3)->create();

    $response = $this->getJson('/api/animals');

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

test('can create an animal', function () {
    $data = [
        'name' => 'Lion',
        'specie' => 'Panthera leo',
        'preferred_environment' => 'Savannah',
    ];

    $response = $this->postJson('/api/animals', $data);

    $response->assertStatus(201)
        ->assertJsonPath('name', 'Lion');

    $this->assertDatabaseHas('animals', $data);
});

test('can create animal with valid enclosure', function () {
    $enclosure = Enclosure::factory()->create(['type' => 'Savannah', 'capacity' => 10]);

    $response = $this->postJson('/api/animals', [
        'name' => 'Lion',
        'specie' => 'Panthera leo',
        'preferred_environment' => 'Savannah',
        'enclosure_id' => $enclosure->id,
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('enclosure_id', $enclosure->id);
});

test('rejects animal when environment does not match', function () {
    $enclosure = Enclosure::factory()->create(['type' => 'Savannah', 'capacity' => 10]);

    $response = $this->postJson('/api/animals', [
        'name' => 'Penguin',
        'specie' => 'Spheniscidae',
        'preferred_environment' => 'Aquatic',
        'enclosure_id' => $enclosure->id,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['enclosure_id']);
});

test('rejects animal when enclosure is full', function () {
    $enclosure = Enclosure::factory()->create(['type' => 'Savannah', 'capacity' => 1]);
    Animal::factory()->create(['enclosure_id' => $enclosure->id]);

    $response = $this->postJson('/api/animals', [
        'name' => 'Lion',
        'specie' => 'Panthera leo',
        'preferred_environment' => 'Savannah',
        'enclosure_id' => $enclosure->id,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['enclosure_id']);
});

test('can transfer animal to valid enclosure', function () {
    $sourceEnclosure = Enclosure::factory()->create(['type' => 'Savannah', 'capacity' => 10]);
    $targetEnclosure = Enclosure::factory()->create(['type' => 'Savannah', 'capacity' => 10]);
    $animal = Animal::factory()->create([
        'preferred_environment' => 'Savannah',
        'enclosure_id' => $sourceEnclosure->id,
    ]);

    $response = $this->postJson("/api/animals/{$animal->id}/transfer", [
        'target_enclosure_id' => $targetEnclosure->id,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('enclosure_id', $targetEnclosure->id);
});

test('rejects transfer when environment does not match', function () {
    $sourceEnclosure = Enclosure::factory()->create(['type' => 'Savannah', 'capacity' => 10]);
    $targetEnclosure = Enclosure::factory()->create(['type' => 'Forest', 'capacity' => 10]);
    $animal = Animal::factory()->create([
        'preferred_environment' => 'Savannah',
        'enclosure_id' => $sourceEnclosure->id,
    ]);

    $response = $this->postJson("/api/animals/{$animal->id}/transfer", [
        'target_enclosure_id' => $targetEnclosure->id,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['enclosure_id']);
});

test('can show an animal', function () {
    $animal = Animal::factory()->create();

    $response = $this->getJson("/api/animals/{$animal->id}");

    $response->assertStatus(200)
        ->assertJsonPath('id', $animal->id);
});

test('can update an animal', function () {
    $animal = Animal::factory()->create(['name' => 'Old Name']);

    $response = $this->putJson("/api/animals/{$animal->id}", [
        'name' => 'New Name',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('name', 'New Name');
});

test('can delete an animal', function () {
    $animal = Animal::factory()->create();

    $response = $this->deleteJson("/api/animals/{$animal->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('animals', ['id' => $animal->id]);
});
