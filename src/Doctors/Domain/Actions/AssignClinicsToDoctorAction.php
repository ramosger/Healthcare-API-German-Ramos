<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Lightit\Doctors\Domain\DataTransferObjects\AssignClinicsToDoctorDto;
use Lightit\Doctors\Domain\Models\Doctor;

final readonly class AssignClinicsToDoctorAction
{
    public function execute(Doctor $doctor, AssignClinicsToDoctorDto $dto): Doctor
    {
        $doctor->clinics()->sync($dto->clinicIds);

        return $doctor->load('clinics:id,name');
    }
}
