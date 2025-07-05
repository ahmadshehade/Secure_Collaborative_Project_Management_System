<?php

namespace App\Mail\Task;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskMakeMail extends Mailable
{
    use Queueable, SerializesModels;
    

    public $task;
    public $createdBy;
    /**
     * Create a new message instance.
     */
    public function __construct( $task,$createdBy)
    {
        $this->task = $task;
        $this->createdBy = $createdBy;
    }


   /**
    * Summary of build
    * @return TaskMakeMail
    */
   public function build(){
    return $this->subject("Give New Task")
    ->view('emails.Task.make-task')
    ->with(['task'=>$this->task,'createdBy'=>$this->createdBy]);
   }
}
