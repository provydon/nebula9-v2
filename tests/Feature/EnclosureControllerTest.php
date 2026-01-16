<?php

use App\Models\Enclosure;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list enclosures', function () {
    Enclosure::factory()->count(3)->create();

    $response = $this->getJson('/api/enclosures');

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

test('can filter enclosures by type', function () {
    Enclosure::factory()->create(['type' => 'Savannah']);
    Enclosure::factory()->create(['type' => 'Forest']);

    $response = $this->getJson('/api/enclosures?type=Savannah');

    $response->assertStatus(200)
        ->assertJsonCount(1)
        ->assertJsonPath('0.type', 'Savannah');
});

test('can create an enclosure', function () {
    $data = [
        'name' => 'Test Enclosure',
        'type' => 'Savannah',
        'capacity' => 10,
    ];

    $response = $this->postJson('/api/enclosures', $data);

    $response->assertStatus(201)
        ->assertJsonPath('name', 'Test Enclosure');

    $this->assertDatabaseHas('enclosures', $data);
});

test('can show an enclosure', function () {
    $enclosure = Enclosure::factory()->create();

    $response = $this->getJson("/api/enclosures/{$enclosure->id}");

    $response->assertStatus(200)
        ->assertJsonPath('id', $enclosure->id);
});

test('can update an enclosure', function () {
    $enclosure = Enclosure::factory()->create(['name' => 'Old Name']);

    $response = $this->putJson("/api/enclosures/{$enclosure->id}", [
        'name' => 'New Name',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('name', 'New Name');
});

test('can delete an enclosure', function () {
    $enclosure = Enclosure::factory()->create();

    $response = $this->deleteJson("/api/enclosures/{$enclosure->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('enclosures', ['id' => $enclosure->id]);
});
