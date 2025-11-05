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
        return Appointment::query()
            ->with(['doctor', 'patient', 'clinic'])
            ->where('user_id', $user->id)
            ->latest('start_date')
            ->paginate(10);
    }
}
