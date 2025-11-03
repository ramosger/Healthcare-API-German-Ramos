<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\DataTransferObjects;

readonly class AssignDoctorsToClinicDto
{
    public function __construct(
        public array $doctorIds,
    ) {
    }
}
