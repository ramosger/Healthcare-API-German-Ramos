<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Clinics\App\Resources\ClinicResource;
use Lightit\Doctors\App\Resources\DoctorResource;
use Lightit\Patients\App\Resources\PatientResource;
use Lightit\Users\App\Resources\UserResource;

/**
 * @mixin Appointment
 */
#[SchemaName('Appointment')]
class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'doctor_id' => $this->doctor_id,
            'doctor' => DoctorResource::make($this->whenLoaded('doctor')),
            'patient_id' => $this->patient_id,
            'patient' => PatientResource::make($this->whenLoaded('patient')),
            'user' => UserResource::make($this->whenLoaded('user')),
            'clinic_id' => $this->clinic_id,
            'clinic' => ClinicResource::make($this->whenLoaded('clinic')),
            'start_date'=> $this->start_date,
            'end_date'=> $this->end_date,
        ];
    }
}
