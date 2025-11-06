<?php

declare(strict_types=1);

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

test('description: blocks unauthenticated POST /appointments', function (): void {
    $payload = [
        'doctor_id' => 1,
        'patient_id' => 1,
        'clinic_id'  => 1,
        'start_date' => now()->addHour()->toISOString(),
        'end_date'   => now()->addHours(2)->toISOString(),
    ];

    postJson('/api/appointments', $payload)->assertUnauthorized();
});

test('blocks unauthenticated DELETE /appointments/{id}', function (): void {
    deleteJson('/api/appointments/1')->assertUnauthorized();
});

test('blocks unauthenticated GET /appointments/me/appointments', function (): void {
    getJson('/api/appointments/me/appointments')->assertUnauthorized();
});
