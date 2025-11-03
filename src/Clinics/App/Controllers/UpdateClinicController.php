<?php

declare(strict_types=1);

namespace Lightit\Clinics\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Clinics\App\Requests\UpsertClinicRequest;
use Lightit\Clinics\App\Resources\ClinicResource;
use Lightit\Clinics\Domain\Actions\UpdateClinicAction;
use Lightit\Clinics\Domain\Models\Clinic;

#[Group('Clinics')]
final readonly class UpdateClinicController
{
    public function __invoke(
        Clinic $clinicToUpdate,
        UpsertClinicRequest $request,
        UpdateClinicAction $action,
    ): JsonResponse {
        $clinicUpdated = $action->execute($clinicToUpdate, $request->toDto());

        return ClinicResource::make($clinicUpdated)
            ->response();
    }
}
