<?php

declare(strict_types=1);

namespace Lightit\Clinics\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Clinics\App\Requests\AssignDoctorsToClinicRequest;
use Lightit\Clinics\App\Resources\ClinicResource;
use Lightit\Clinics\Domain\Actions\AssignDoctorsToClinicAction;
use Lightit\Clinics\Domain\Models\Clinic;

#[Group('Clinics')]
final readonly class AssignDoctorsToClinicController
{
    public function __invoke(
        Clinic $clinic,
        AssignDoctorsToClinicRequest $request,
        AssignDoctorsToClinicAction $action,
    ): JsonResponse {
        $clinic = $action->execute($clinic, $request->toDto());

        return ClinicResource::make($clinic)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_OK);
    }
}
