<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailReminder extends Notification implements ShouldQueue
{
    use Queueable;

    private $_subject = null;
    private $_path = null;
    private $_data = null;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($subject, $path, $data = null)
    {
        $this->_subject = $subject;
        $this->_path = $path;
        $this->_data = $data;
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
        // return (new MailMessage)->view($this->_path)->from('greengen@crm-crisaloid.com', 'PORTALE GREENGEN')->subject($this->_subject);
        return (new MailMessage)->view($this->_path)->from('greengen@crm-labloid.com', 'PORTALE GREENGEN')->subject($this->_subject);
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
