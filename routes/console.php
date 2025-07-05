<?php

use App\Jobs\MarkOverdueTasksJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new MarkOverdueTasksJob)->dailyAt('00:00');
Schedule::command('app:clean-old-attachments')->cron('0 2 1 */3 *');
