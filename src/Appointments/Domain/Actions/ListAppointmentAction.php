<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Users\Domain\Models\User;

class ListAppointmentAction
{
    /**
     * @return LengthAwarePaginator<int, Appointment>
     */
    public function execute(User $user): LengthAwarePaginator
    {
        return $user->appointments()
            ->with(['doctor', 'patient', 'clinic'])
            ->latest('start_date')
            ->paginate(10);
    }
}
