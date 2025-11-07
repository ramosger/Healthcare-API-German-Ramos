<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\DataTransferObjects;

use Carbon\CarbonImmutable;

readonly class AppointmentDto
{
    public function __construct(
        public int $doctor_id,
        public int $patient_id,
        public int $clinic_id,
        public CarbonImmutable $start_date,
        public CarbonImmutable $end_date,
    ) {
    }
}
