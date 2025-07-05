<?php

namespace App\Mail\Task;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

  public $task;
    public $updatedBy;
    /**
     * Create a new message instance.
     */
    public function __construct( $task,$updatedBy)
    {
        $this->task = $task;
        $this->updatedBy = $updatedBy;
    }

    /**
     * Summary of build
     * @return TaskUpdateMail
     */
    public function build(){
        return $this->subject('Update Task')
        ->view('emails.Task.update-task')
        ->with(['task'=>$this->task,'updatedBy'=>$this->updatedBy]);
    }
}
