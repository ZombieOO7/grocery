<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailNotification extends Mailable
{
    use Queueable, SerializesModels;
    protected $emailData;
    protected $sub;
    protected $usersData;
    protected $file;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailData, $sub, $usersData, $file)
    {
        $this->emailData = $emailData;
        $this->sub = $sub;
        $this->usersData = $usersData;
        $this->file = $file;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
                ->subject($this->sub)
                ->markdown($this->file, ['user' => $this->usersData, 'emailData' => $this->emailData]);
    }
}
