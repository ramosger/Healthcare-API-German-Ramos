<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Illuminate\Support\Collection;
use Lightit\Doctors\Domain\Models\Doctor;

class ListDoctorAction
{
    /**
     * @return Collection<int, Doctor>
     */
    public function execute(): Collection
    {
        return Doctor::query()
            ->orderBy('name')
            ->get();
    }
}
