<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Actions\ListAppointmentAction;
use Lightit\Users\Domain\Models\User;

#[Group('Appointments')]
final class ListAppointmentController
{
    public function __invoke(
        #[CurrentUser]
        User $user,
        ListAppointmentAction $action,
    ): JsonResponse {
        $appointments = $action->execute($user);

        return AppointmentResource::collection($appointments)
            ->response();
    }
}
