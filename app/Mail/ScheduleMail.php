<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScheduleMail extends Mailable
{
    use Queueable, SerializesModels;
 
    protected $param;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $param)
    {
        $this->param = $param;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::debug($this->param);

        return $this
            ->from('info@grouptube.biz','GROUPTUBE')
            ->to($this->param['email'])
            ->bcc($this->param['my_email'])
            ->subject($this->param['title'])
            ->view('mail.scheduleMail')
            ->with([
                'message_text' => $this->param['message_text'],
                'schedule' => $this->param['schedule'],
                'booking' => $this->param['booking'],
        ]);
    }
}
