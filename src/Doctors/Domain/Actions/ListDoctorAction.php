<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Lightit\Doctors\Domain\Models\Doctor;

class ListDoctorAction
{
    /**
     * @return LengthAwarePaginator<int, Doctor>
     */
    public function execute(): LengthAwarePaginator
    {
        return Doctor::query()
            ->orderBy('name')
            ->paginate(10);
    }
}
