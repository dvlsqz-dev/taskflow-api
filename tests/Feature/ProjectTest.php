<?php

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can retrieve a list of projects', function () {
    $user = User::factory()->create();
    Project::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/projects');

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

it('can create a project', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user, 'sanctum')->postJson('/api/projects', [
        'name' => 'Test Project',
        'description' => 'This is a test project.',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'created_at']
        ]);

    $this->assertDatabaseHas('projects', [
        'name' => 'Test Project',
        'description' => 'This is a test project.',
    ]);
});

it('can view a single project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')->getJson("/api/projects/{$project->id}");

    $response->assertStatus(200)
         ->assertJsonPath('data.id', $project->id)
         ->assertJsonPath('data.name', $project->name)
         ->assertJsonPath('data.description', $project->description);
});

it('can not view someone else project', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($intruder, 'sanctum')->getJson("/api/projects/{$project->id}");
    $response->assertStatus(403);
});

it('can delete a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/projects/{$project->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Project deleted successfully',
        ]);

    $this->assertSoftDeleted('projects', ['id' => $project->id]);
});