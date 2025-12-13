<?php

namespace Tests\Feature;

use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResidentValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user and authenticate
        $this->user = User::factory()->create();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_resident_age_must_be_adult()
    {
        $response = $this->actingAs($this->user)
            ->post(route('residents.store'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'gender' => 'Male',
                'status' => 'Active',
                'age' => 17, // Invalid
            ]);

        $response->assertSessionHasErrors(['age']);
    }

    public function test_resident_can_be_created_with_adult_age()
    {
        $response = $this->actingAs($this->user)
            ->post(route('residents.store'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'gender' => 'Male',
                'status' => 'Active',
                'age' => 18, // Valid
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('residents', ['first_name' => 'John', 'last_name' => 'Doe']);
    }

    public function test_cannot_create_duplicate_resident_name()
    {
        Resident::create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'Female',
            'status' => 'Active',
            'age' => 20
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('residents.store'), [
                'first_name' => 'Jane',
                'last_name' => 'Doe', // Duplicate
                'gender' => 'Female',
                'status' => 'Active',
                'age' => 25
            ]);

        $response->assertSessionHasErrors(['first_name']);
    }

    public function test_can_update_resident_with_same_name()
    {
        $resident = Resident::create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'Female',
            'status' => 'Active',
            'age' => 20
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('residents.update', $resident), [
                'first_name' => 'Jane', // Same as before
                'last_name' => 'Doe',
                'gender' => 'Female',
                'status' => 'Active',
                'age' => 21
            ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_cannot_update_resident_to_existing_name()
    {
        Resident::create([
            'first_name' => 'Existing',
            'last_name' => 'Name',
            'gender' => 'Male',
            'status' => 'Active',
            'age' => 30
        ]);

        $resident = Resident::create([
            'first_name' => 'New',
            'last_name' => 'Name',
            'gender' => 'Male',
            'status' => 'Active',
            'age' => 30
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('residents.update', $resident), [
                'first_name' => 'Existing', // Collision
                'last_name' => 'Name',
                'gender' => 'Male',
                'status' => 'Active',
                'age' => 30
            ]);

        $response->assertSessionHasErrors(['first_name']);
    }
}
