<?php

declare(strict_types=1);

namespace Lightit\Patients\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lightit\Patients\Domain\DataTransferObjects\PatientDto;
use Lightit\Patients\Domain\Models\Patient;
use Lightit\Users\Domain\Models\User;

final class UpsertPatientRequest extends FormRequest
{
    public const NAME = 'name';

    public const EMAIL = 'email';

    public const USER_ID = 'user_id';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            self::NAME => ['required', 'string', 'min:4', 'max:80'],
            self::EMAIL => [
                'required',
                'string',
                'max:90',
                Rule::email()->strict(),
                Rule::unique(Patient::class)->ignore($this->id),
            ],
            self::USER_ID => ['required', 'integer', 'min:1', Rule::exists(User::class, 'id')],
        ];
    }

    public function toDto(): PatientDto
    {
        return new PatientDto(
            name: $this->string(self::NAME)->toString(),
            email: $this->string(self::EMAIL)->toString(),
            user_id: $this->integer(self::USER_ID),
        );
    }
}
