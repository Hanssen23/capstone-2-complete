<?php

use App\Models\RfidLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('rfid logs endpoint returns paginated results', function () {
    // Create 25 RFID logs
    RfidLog::factory()->count(25)->create();
    
    // Test first page (should return 10 items)
    $response = $this->get('/rfid/logs');
    
    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'logs' => [
                'current_page',
                'data',
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total'
            ]
        ]);
    
    $data = $response->json();
    expect($data['logs']['per_page'])->toBe(10);
    expect($data['logs']['current_page'])->toBe(1);
    expect($data['logs']['total'])->toBe(25);
    expect($data['logs']['last_page'])->toBe(3);
    expect(count($data['logs']['data']))->toBe(10);
});

test('rfid logs pagination works with page parameter', function () {
    // Create 25 RFID logs
    RfidLog::factory()->count(25)->create();
    
    // Test second page
    $response = $this->get('/rfid/logs?page=2');
    
    $response->assertStatus(200);
    
    $data = $response->json();
    expect($data['logs']['current_page'])->toBe(2);
    expect(count($data['logs']['data']))->toBe(10);
});

test('rfid logs pagination works with filters', function () {
    // Create logs with different actions
    RfidLog::factory()->count(5)->create(['action' => 'check_in']);
    RfidLog::factory()->count(5)->create(['action' => 'check_out']);
    
    // Test with action filter
    $response = $this->get('/rfid/logs?action=check_in');
    
    $response->assertStatus(200);
    
    $data = $response->json();
    expect($data['logs']['total'])->toBe(5);
    expect($data['logs']['current_page'])->toBe(1);
    expect(count($data['logs']['data']))->toBe(5);
});

