<?php

declare(strict_types=1);

namespace Lightit\Clinics\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Lightit\Clinics\Domain\DataTransferObjects\ClinicDto;

final class UpsertClinicRequest extends FormRequest
{
    public const NAME = 'name';

    public const ADDRESS = 'address';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            self::NAME => ['required', 'string', 'min:4', 'max:80'],
            self::ADDRESS => ['required', 'string', 'min:6', 'max:90'],
        ];
    }

    public function toDto(): ClinicDto
    {
        return new ClinicDto(
            name: $this->string(self::NAME)->toString(),
            address: $this->string(self::ADDRESS)->toString(),
        );
    }
}
