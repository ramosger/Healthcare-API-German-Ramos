<?php

declare(strict_types=1);

namespace Lightit\Patients\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Patients\App\Requests\UpsertPatientRequest;
use Lightit\Patients\App\Resources\PatientResource;
use Lightit\Patients\Domain\Actions\UpdatePatientAction;
use Lightit\Patients\Domain\Models\Patient;

#[Group('Patients')]
final readonly class UpdatePatientController
{
    public function __invoke(
        Patient $patientId,
        UpsertPatientRequest $request,
        UpdatePatientAction $action,
    ): JsonResponse {
        $patientUpdated = $action->execute($patientId, $request->toDto());

        return PatientResource::make($patientUpdated)
            ->response();
    }
}
