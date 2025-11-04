<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Doctors\App\Requests\AssignClinicsToDoctorRequest;
use Lightit\Doctors\App\Resources\DoctorResource;
use Lightit\Doctors\Domain\Actions\AssignClinicsToDoctorAction;
use Lightit\Doctors\Domain\Models\Doctor;

#[Group('Doctors')]
final readonly class AssignClinicsToDoctorController
{
    public function __invoke(
        Doctor $doctor,
        AssignClinicsToDoctorRequest $request,
        AssignClinicsToDoctorAction $action,
    ): JsonResponse {
        $doctor = $action->execute($doctor, $request->toDto());

        return DoctorResource::make($doctor)
            ->response();
    }
}
