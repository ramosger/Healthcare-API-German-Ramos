<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Lightit\Appointments\Domain\Models\Appointment;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::now();
        $end   = (clone $start)->addMinutes(rand(30, 120));

        return [
            'doctor_id' => DoctorFactory::new(),
            'patient_id' => PatientFactory::new(),
            'clinic_id'  => ClinicFactory::new(),
            'start_date' => $start,
            'end_date' => $end,
        ];
    }
}
