<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\Actions;

use Lightit\Clinics\Domain\DataTransferObjects\AssignDoctorsToClinicDto;
use Lightit\Clinics\Domain\Models\Clinic;

final readonly class AssignDoctorsToClinicAction
{
    public function execute(Clinic $clinic, AssignDoctorsToClinicDto $dto): Clinic
    {
        $clinic->doctors()->sync($dto->doctorIds);

        return $clinic->load('doctors:id,name');
    }
}
