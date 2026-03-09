<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VoucherControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_voucher_success()
    {
        $response = $this->postJson('/api/generate', [
            'name' => 'John Doe',
            'id' => 'CR001',
            'aircraft' => 'Airbus 320',
            'flightNumber' => 'GA123',
            'date' => '2026-03-10',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_generate_voucher_validation_error()
    {
        $response = $this->postJson('/api/generate', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'id',
                'aircraft',
                'flightNumber',
                'date'
            ]);
    }

    public function test_generate_voucher_duplicate_error()
    {
        $this->postJson('/api/generate', [
            'name' => 'John Doe',
            'id' => 'CR001',
            'aircraft' => 'Airbus 320',
            'flightNumber' => 'GA123',
            'date' => '2026-03-10',
        ]);


        $response = $this->postJson('/api/generate', [
            'name' => 'John Doe',
            'id' => 'CR001',
            'aircraft' => 'Airbus 320',
            'flightNumber' => 'GA123',
            'date' => '2026-03-10',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'message' => 'Voucher already exists'
            ]);
    }

    public function test_check_voucher_exists()
    {
        $this->postJson('/api/generate', [
            'name' => 'John Doe',
            'id' => 'CR001',
            'aircraft' => 'Airbus 320',
            'flightNumber' => 'GA123',
            'date' => '2026-03-10',
        ]);

        $response = $this->postJson('/api/check', [
            'flightNumber' => 'GA123',
            'date' => '2026-03-10'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'exists' => true
            ]);
    }

    public function test_check_voucher_success()
    {
        $response = $this->postJson('/api/check', [
            'flightNumber' => 'GA123',
            'date' => '2026-03-10'
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
            ]);
    }

    public function test_check_voucher_validation_error()
    {
        $response = $this->postJson('/api/check', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'flightNumber',
                'date'
            ]);
    }
}
