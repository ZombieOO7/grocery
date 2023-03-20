<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\EmailNotification;
use Mail;

class SingleEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailData;
    protected $subject;
    protected $usersData;
    protected $file;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emailData, $subject, $usersData, $file)
    {
        $this->emailData = $emailData;
        $this->subject = $subject;
        $this->usersData = $usersData;
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sendMail($this->emailData, $this->subject, $this->usersData, $this->file);
    }

    public function sendMail($emailData, $subject, $usersData , $file) {
        Mail::to($usersData->email)->send((new EmailNotification($emailData, $subject, $usersData, $file)));
    }
}
