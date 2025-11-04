<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Lightit\Clinics\Domain\Models\Clinic;

class ListClinicAction
{
    /**
     * @return LengthAwarePaginator<int, Clinic>
     */
    public function execute(): LengthAwarePaginator
    {
        return Clinic::query()
            ->orderBy('name')
            ->paginate(10);
    }
}
