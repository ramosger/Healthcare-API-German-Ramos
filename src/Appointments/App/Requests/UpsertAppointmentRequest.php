<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Requests;

use Carbon\CarbonImmutable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Patients\Domain\Models\Patient;

final class UpsertAppointmentRequest extends FormRequest
{
    public const DOCTOR_ID = 'doctor_id';

    public const PATIENT_ID = 'patient_id';

    public const CLINIC_ID = 'clinic_id';

    public const START_DATE = 'start_date';

    public const END_DATE = 'end_date';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->authenticatedUserId();

        return [
            self::DOCTOR_ID => [
                'required',
                'integer',
                'min:1',
                Rule::exists(Doctor::class, 'id'),
                Rule::exists('clinic_doctor', 'doctor_id')->where(
                    fn (Builder $q): Builder =>
                    $q->where('clinic_id', $this->integer(self::CLINIC_ID))
                ),
            ],
            self::CLINIC_ID => ['required', 'integer', 'min:1', Rule::exists(Clinic::class, 'id'), ],
            self::PATIENT_ID => [
                'required',
                'integer',
                'min:1',
                Rule::exists(Patient::class, 'id')->where(
                    fn (Builder $q): Builder =>
                    $q->where('user_id', $userId)
                ),
            ],
            self::START_DATE => ['required', 'date', 'after:now'],
            self::END_DATE => ['required', 'date', 'after:' . self::START_DATE],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $v): void {
                $doctorId = $this->integer(self::DOCTOR_ID);
                $patientId = $this->integer(self::PATIENT_ID);
                $clinicId = $this->integer(self::CLINIC_ID);
                $startRaw = $this->string(self::START_DATE)->toString();
                $endRaw = $this->string(self::END_DATE)->toString();

                if (! $doctorId || ! $patientId || ! $clinicId || ! $startRaw || ! $endRaw) {
                    return;
                }

                $start = CarbonImmutable::parse($startRaw);
                $end = CarbonImmutable::parse($endRaw);

                $overlapScope = $this->calculateOverlapScope($start, $end);

                $doctorOverlap = Appointment::query()
                    ->where('doctor_id', $doctorId)
                    ->where('clinic_id', $clinicId)
                    ->tap($overlapScope)
                    ->exists();

                if ($doctorOverlap) {
                    $v->errors()->add(
                        self::START_DATE,
                        'The Doctor already has an appointment in the selected date range'
                    );
                }

                $patientOverlap = Appointment::query()
                    ->where('patient_id', $patientId)
                    ->tap($overlapScope)
                    ->exists();

                if ($patientOverlap) {
                    $v->errors()->add(
                        self::START_DATE,
                        'The Patient already has an appointment in the selected date range'
                    );
                }
            },
        ];
    }

    public function toDto(): AppointmentDto
    {
        return new AppointmentDto(
            doctor_id: $this->integer(self::DOCTOR_ID),
            patient_id: $this->integer(self::PATIENT_ID),
            clinic_id: $this->integer(self::CLINIC_ID),
            start_date: CarbonImmutable::parse($this->string(self::START_DATE)->toString()),
            end_date: CarbonImmutable::parse($this->string(self::END_DATE)->toString()),
        );
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            self::DOCTOR_ID . '.exists' => 'The doctor is not assigned to the selected clinic',
            self::PATIENT_ID . '.exists' => 'The patient does not belong to the authenticated user',
            self::END_DATE . '.after' => 'End Date must be greater than the Start Date',
            self::START_DATE . '.after' => 'You can`t book an appointment in the past',
        ];
    }

    private function calculateOverlapScope(CarbonImmutable $start, CarbonImmutable $end): callable
    {
        return static fn (EloquentBuilder $q): EloquentBuilder =>
            $q
                ->where('start_date', '<', $end)
                ->where('end_date', '>', $start);
    }

    private function authenticatedUserId(): int|string
    {
        $user = $this->user();

        if ($user === null) {
            throw new AuthenticationException();
        }

        /** @var int|string */
        $userId = $user->getKey();

        return $userId;
    }
}
