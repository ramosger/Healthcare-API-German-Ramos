<?php

declare(strict_types=1);

namespace Lightit\Patients\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Patients\App\Resources\PatientResource;
use Lightit\Patients\Domain\Models\Patient;

#[Group('Patients')]
final class GetPatientController
{
    public function __invoke(Patient $patient): JsonResponse
    {
        return PatientResource::make($patient)
            ->response();
    }
}
