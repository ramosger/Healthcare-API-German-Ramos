<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Lightit\Doctors\Domain\Models\Doctor;

final class DoctorBelongsToClinic implements ValidationRule
{
    public function __construct(
        private readonly int $clinicId,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $doctorId = $this->normalizeInt($value);

        $exists = Doctor::query()
            ->whereHas('clinics', fn (Builder $q) => $q->whereKey($this->clinicId))
            ->whereKey($doctorId)
            ->exists();

        if (! $exists) {
            $fail('The doctor is not assigned to the selected clinic.');
        }
    }

    private function normalizeInt(mixed $v): int|null
    {
        if (is_int($v)) {
            return $v;
        }

        if (is_string($v) && $v !== '' && ctype_digit($v)) {
            return (int) $v;
        }

        return null;
    }
}
