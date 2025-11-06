<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Appointments\Domain\Guards\AppointmentGuard;

uses(RefreshDatabase::class);

require_once __DIR__ . '/../../Datasets/InvalidAppointments.php';

describe('AppointmentGuard::assertCanCreate', function (): void {
    it('throws DomainException for invalid appointments', function (AppointmentDto $dto, string $message): void {
        expect(fn () => call_user_func([new AppointmentGuard(), 'assertCanCreate'], $dto))
            ->toThrow(\DomainException::class, $message);
    })->with('invalid-appointments');
});
