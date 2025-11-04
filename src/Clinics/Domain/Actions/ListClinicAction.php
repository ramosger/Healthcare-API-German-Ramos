<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\Actions;

use Illuminate\Support\Collection;
use Lightit\Clinics\Domain\Models\Clinic;

class ListClinicAction
{
    /**
     * @return Collection<int, Clinic>
     */
    public function execute(): Collection
    {
        return Clinic::query()
            ->orderBy('name')
            ->get();
    }
}
