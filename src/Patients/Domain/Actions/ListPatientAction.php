<?php

declare(strict_types=1);

namespace Lightit\Patients\Domain\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Lightit\Patients\Domain\Models\Patient;

class ListPatientAction
{
    /**
     * @return LengthAwarePaginator<int, Patient>
     */
    public function execute(): LengthAwarePaginator
    {
        return Patient::query()
            ->orderBy('name')
            ->paginate(10);
    }
}
