<?php

declare(strict_types=1);

namespace Lightit\Clinics\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lightit\Clinics\Domain\DataTransferObjects\AssignDoctorsToClinicDto;
use Lightit\Doctors\Domain\Models\Doctor;

final class AssignDoctorsToClinicRequest extends FormRequest
{
    public const DOCTOR_IDS = 'doctor_ids';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            self::DOCTOR_IDS => ['required', 'array', 'min:1'],
            self::DOCTOR_IDS . '.*' => ['integer', Rule::exists(Doctor::class)],
        ];
    }

    public function toDto(): AssignDoctorsToClinicDto
    {
        /** @var array<int, int|string> */
        $ids = $this->validated(self::DOCTOR_IDS) ?? [];

        return new AssignDoctorsToClinicDto(
            doctorIds: array_map(static fn (int|string $id): int => (int) $id, $ids),
        );
    }
}
