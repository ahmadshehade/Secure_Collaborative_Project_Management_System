<?php

namespace App\Providers;

use App\Exceptions\Handler;
use App\Interfaces\Repositories\Attachments\AttachmentRepositoryInterface;
use App\Interfaces\Repositories\Auth\AuthenticateRepositoryInterface;
use App\Interfaces\Repositories\Comments\CommentRepositoryInterface;
use App\Interfaces\Repositories\Notifications\NotificationRepositoryInterface;
use App\Interfaces\Repositories\Projects\ProjectRepositoryInterface;
use App\Interfaces\Repositories\Tasks\TaskRepositoryInterface;
use App\Interfaces\Repositories\Teams\TeamRepositoryInterface;
use App\Interfaces\Repositories\Users\UserRepositoryInterface;
use App\Interfaces\Services\Attachments\AttachmentInterface;
use App\Interfaces\Services\Auth\AuthenticateServiceInterface;
use App\Interfaces\Services\Comments\CommentInterface;
use App\Interfaces\Services\Notifications\NotificationInterface;
use App\Interfaces\Services\Projects\ProjectInterface;
use App\Interfaces\Services\Tasks\TaskInterface;
use App\Interfaces\Services\Teams\TeamInterface;
use App\Interfaces\Services\Users\UserInterface;
use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Observers\AttachmentObserver;
use App\Observers\ProjectObserver;
use App\Observers\TaskObserver;
use App\Observers\TeamObserver;
use App\Repositories\Attachments\AttachmentsRepository;
use App\Repositories\Auth\AuthenticateRepository;
use App\Repositories\Comments\CommentRepository;
use App\Repositories\Notifications\NotificationRepository;
use App\Repositories\Projects\ProjectRepository;
use App\Repositories\Tasks\TaskRepository;
use App\Repositories\Teams\TeamRepository;
use App\Repositories\Users\UserRepository;
use App\Services\Attachments\AttachmentService;
use App\Services\Auth\AuthenticateService;
use App\Services\Comments\CommentService;
use App\Services\Notifications\NotificationService;
use App\Services\Projects\ProjectService;
use App\Services\Tasks\TaskService;
use App\Services\Teams\TeamService;
use App\Services\Users\UserService;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         
        $this->app->singleton(ExceptionHandler::class, Handler::class);
        
        $this->app->bind(AuthenticateServiceInterface::class, AuthenticateService::class);
        $this->app->bind(AuthenticateRepositoryInterface::class, AuthenticateRepository::class);

        $this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);
        $this->app->bind(TeamInterface::class, TeamService::class);

        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(ProjectInterface::class, ProjectService::class);


        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(TaskInterface::class, TaskService::class);


        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
        $this->app->bind(CommentInterface::class, CommentService::class);


        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserInterface::class, UserService::class);


        $this->app->bind(AttachmentRepositoryInterface::class, AttachmentsRepository::class);
        $this->app->bind(AttachmentInterface::class, AttachmentService::class);


        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(NotificationInterface::class, NotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Task::observe(TaskObserver::class);
        Attachment::observe(AttachmentObserver::class);
        Team::observe(TeamObserver::class);
        Project::observe(ProjectObserver::class);
    }
}
