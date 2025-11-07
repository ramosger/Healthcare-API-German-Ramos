<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Date;
use Lightit\Appointments\Domain\Models\Appointment;

class AppointmentCreated extends Notification implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable;

    public function __construct(private Appointment $appointment)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appointment = $this->appointment->loadMissing(['doctor', 'patient', 'clinic']);

        $startDate = $appointment->start_date
            ? Date::parse($appointment->start_date)->format('Y-m-d H:i:s')
            : null;

        $mail = new MailMessage();
        $mail->subject('Appointment Confirmation');
        $mail->view('mail.appointment.created', [
            'appointment' => $appointment,
            'doctor' => $appointment->doctor,
            'patient' => $appointment->patient,
            'clinic' => $appointment->clinic,
            'user' => $notifiable,
            'startDate' => $startDate,
        ]);

        return $mail;
    }

    /**
     * @return array<string, string>
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'mail',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [

        ];
    }
}
