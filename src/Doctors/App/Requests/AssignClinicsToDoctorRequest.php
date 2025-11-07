<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Doctors\Domain\DataTransferObjects\AssignClinicsToDoctorDto;
use Lightit\Shared\App\Rules\AllExistIn;

final class AssignClinicsToDoctorRequest extends FormRequest
{
    public const CLINIC_IDS = 'clinic_ids';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            self::CLINIC_IDS => ['required', 'array', 'min:1', new AllExistIn(Clinic::class, 'id')],
            self::CLINIC_IDS . '.*' => ['integer', 'distinct'],
        ];
    }

    public function toDto(): AssignClinicsToDoctorDto
    {
        /** @var array<int, int|string> */
        $ids = $this->validated(self::CLINIC_IDS) ?? [];

        return new AssignClinicsToDoctorDto(
            clinicIds: array_map(static fn (int|string $id): int => (int) $id, $ids),
        );
    }
}
