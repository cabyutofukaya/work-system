<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TodoAlertMail extends Mailable
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
        return $this
            ->from('info@grouptube.biz','GROUPTUBE')
            ->to($this->param['email'])
            ->bcc('postmaster@grouptube.biz')
            ->subject($this->param['count'] . '件のTODOがあります。')
            ->view('mail.toDoAlert')
            ->with([
                'sales_todo' => $this->param['sales_todo'],
                'office_todo' => $this->param['office_todo'],
                'count' => $this->param['count'],
        ]);
    }
}
