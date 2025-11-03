<?php

declare(strict_types=1);

namespace Lightit\Patients\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Patients\App\Resources\PatientResource;
use Lightit\Patients\Domain\Actions\ListPatientAction;

#[Group('Patients')]
final readonly class ListPatientController
{
    public function __invoke(
        ListPatientAction $action,
    ): JsonResponse {
        $clinics = $action->execute();

        return PatientResource::collection($clinics)
            ->response();
    }
}
