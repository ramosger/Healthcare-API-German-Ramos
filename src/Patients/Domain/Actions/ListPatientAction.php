<?php

declare(strict_types=1);

namespace Lightit\Patients\Domain\Actions;

use Illuminate\Support\Collection;
use Lightit\Patients\Domain\Models\Patient;

class ListPatientAction
{
    /**
     * @return Collection<int, Patient>
     */
    public function execute(): Collection
    {
        return Patient::query()
            ->orderBy('name')
            ->get();
    }
}
