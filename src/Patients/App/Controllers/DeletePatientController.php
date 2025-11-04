<?php

declare(strict_types=1);

namespace Lightit\Patients\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Patients\Domain\Models\Patient;

#[Group('Patients')]
final class DeletePatientController
{
    public function __invoke(Patient $patient): JsonResponse
    {
        $patient->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
