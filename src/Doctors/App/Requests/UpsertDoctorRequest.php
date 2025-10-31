<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Lightit\Doctors\Domain\DataTransferObjects\DoctorDto;
use Lightit\Doctors\Domain\Models\Doctor;

class UpsertDoctorRequest extends FormRequest
{
    public const NAME = 'name';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Doctor|null $doctor */
        $doctor = $this->route('doctor');

        return [
            self::NAME => ['required', 'string', 'min:4', 'max:80'],
        ];
    }

    public function toDto(): DoctorDto
    {
        return new DoctorDto(
            name: $this->string(self::NAME)->toString(),
        );
    }
}
