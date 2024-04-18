<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class materialChangeMail extends Mailable
{
    use Queueable, SerializesModels;


    public $subject;
    public $name;
    public $data;
    public $construct_id;
    public $delation;
    public $user;
    

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $name, $data, $construct_id, $delation = null)
    {
     
        $this->subject = $subject;
        $this->name = $name;
        $this->data = $data;
        $this->construct_id = $construct_id;
        $this->delation = $delation;
        $this->user = Auth()->user()->name;


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
            view: 'emails.material_changing',
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
