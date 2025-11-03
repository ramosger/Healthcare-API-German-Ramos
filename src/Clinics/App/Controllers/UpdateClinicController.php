<?php

declare(strict_types=1);

namespace Lightit\Clinics\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Clinics\App\Resources\ClinicResource;
use Lightit\Clinics\Domain\Actions\UpdateClinicAction;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Clinics\App\Requests\UpsertClinicRequest;

#[Group('Clinics')]
final readonly class UpdateClinicController
{
    public function __invoke(
        Clinic $clinic,
        UpsertClinicRequest $request,
        UpdateClinicAction $updateClinicAction,
    ): JsonResponse {
        $clinic = $updateClinicAction->execute($clinic, $request->toDto());

        return ClinicResource::make($clinic)
            ->response();
    }
}
