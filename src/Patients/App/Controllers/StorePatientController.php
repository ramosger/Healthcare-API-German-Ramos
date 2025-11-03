<?php

declare(strict_types=1);

namespace Lightit\Patients\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Patients\App\Requests\UpsertPatientRequest;
use Lightit\Patients\App\Resources\PatientResource;
use Lightit\Patients\Domain\Actions\StorePatientAction;

#[Group('Clinics')]
final readonly class StorePatientController
{
    public function __invoke(UpsertPatientRequest $request, StorePatientAction $action): JsonResponse
    {
        $patient = $action->execute($request->toDto());

        return PatientResource::make($patient)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
