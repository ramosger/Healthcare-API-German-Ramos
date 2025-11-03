<?php

declare(strict_types=1);

namespace Lightit\Patients\Domain\Actions;

use Lightit\Patients\Domain\DataTransferObjects\PatientDto;
use Lightit\Patients\Domain\Models\Patient;

class StorePatientAction
{
    public function execute(PatientDto $patientDto): Patient
    {
        $patient = new Patient();

        $patient->name = $patientDto->name;
        $patient->email = $patientDto->email;

        $patient->saveOrFail();

        return $patient;
    }
}
