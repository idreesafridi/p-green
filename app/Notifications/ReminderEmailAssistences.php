<?php

namespace App\Notifications;

use App\Models\MaterialsAsisstance;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReminderEmailAssistences extends Notification
{
    use Queueable;

    protected $assistance;

    /**
     * Create a new notification instance.
     *
     * @param MaterialsAsisstance $assistance
     */
    public function __construct(MaterialsAsisstance $assistance)
    {
        $this->assistance = $assistance;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        dd($this->assistance);
        $construction_site = $this->assistance;
        $centre_name = $construction_site->name.' '.$construction_site->surename;
        $expiry_date = Carbon::parse($this->assistance->expiry_date);

        // Define the reminder dates
        $reminder_date_7_days = Carbon::now()->addDays(7);
        $reminder_date_2_days = Carbon::now()->addDays(2);

        $message = '';
        $subject = '';

        // Send reminder email 7 days before the expiry date
        if ($expiry_date->eq($reminder_date_7_days)) {
            $message = 'ATTENZIONE, ASSISTENZA PROGRAMMATA PER \''.$centre_name.'\' TRA 7 GIORNI';
            $subject = '-7 GG ASSISTENZA \''.$centre_name.'\'';
        }

        // Send reminder email 2 days before the expiry date
        if ($expiry_date->eq($reminder_date_2_days)) {
            $message = 'ATTENZIONE, ASSISTENZA PROGRAMMATA PER \''.$centre_name.'\' TRA 2 GIORNI';
            $subject = '-2 GG ASSISTENZA \''.$centre_name.'\'';
        }

        return (new MailMessage)
            ->subject($subject)
            ->line($message)
            ->to('ASSISENTENZE.GREENGEN@GMAIL.COM');
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
