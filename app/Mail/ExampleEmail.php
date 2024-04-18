<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExampleEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $name;
    public $msg;
    public $user;
    public $construction_id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $name, $msg, $construction_id = null)
    {
        $this->subject = $subject;
        $this->name = $name;
        $this->msg = $msg;
        $this->user = optional(Auth()->user())->name;
        $this->construction_id = $construction_id;
        
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->subject
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.material_status',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
