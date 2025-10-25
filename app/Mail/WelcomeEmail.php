<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $name;

    public function __construct($user)
    {
        $this->user = $user;
        $this->name = $user->name;
    }

    public function build()
    {
        return $this->subject('Welcome to Our App')
            ->view('emails.welcome');
    }
}
