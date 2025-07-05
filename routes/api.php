<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Attachments\AttachmentController;
use App\Http\Controllers\Api\Comments\CommentController;
use App\Http\Controllers\Api\Notifications\NotificationController;
use App\Http\Controllers\Api\projects\ProjectController;
use App\Http\Controllers\Api\Tasks\TaskController;
use App\Http\Controllers\Api\Team\TeamController;
use App\Http\Controllers\Api\Users\UserController;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::middleware(['auth:api'])->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);


    Route::middleware(['admin'])->group(function () {

        Route::post('change/role/{id}', [UserController::class, 'update']);
        Route::delete('delete/user/{id}', [UserController::class, 'destroy']);
        Route::get('get/users/{limit}', [UserController::class, 'index']);
        Route::get('get/user/{id}', [UserController::class, 'show']);
    });



    Route::post('make/team', [TeamController::class, 'store']);
    Route::post('update/team/{id}', [TeamController::class, 'update']);
    Route::get('get/teams', [TeamController::class, 'index']);
    Route::get('get/team/{id}', [TeamController::class, 'show']);
    Route::delete('delete/team/{id}', [TeamController::class, 'destroy']);
    Route::get('get/most/active/team', [TeamController::class, 'indexMostActiveTeams']);

    Route::post('make/project', [ProjectController::class, 'store']);
    Route::post('update/project/{id}', [ProjectController::class, 'update']);
    Route::get('get/all/projects', [ProjectController::class, 'index']);
    Route::get('get/project/{id}', [ProjectController::class, 'show']);
    Route::delete('delete/project/{id}', [ProjectController::class, 'destroy']);
    Route::get('get/projects/with/late/task', [ProjectController::class, 'getProjectsWithLateTasks']);


    Route::post('make/task', [TaskController::class, 'store']);
    Route::post('update/task/{id}', [TaskController::class, 'update']);
    Route::get('get/all/tasks', [TaskController::class, 'index']);
    Route::get('get/task/{id}', [TaskController::class, 'show']);
    Route::delete('delete/task/{id}', [TaskController::class, 'destroy']);
    Route::get('get/completed/tasks/count/{projectId}', [TaskController::class, 'getCompletedTasksCount']);


    Route::post('make/comment', [CommentController::class, 'store']);
    Route::post('update/comment/{id}', [CommentController::class, 'update']);
    Route::get('get/All/comment/{type}/{commentId}', [CommentController::class, 'index']);
    Route::get('get/comment/{id}', [CommentController::class, 'show']);
    Route::delete('delete/comment/{id}', [CommentController::class, 'destroy']);


    Route::get('get/all/attachments', [AttachmentController::class, 'index']);
    Route::get('get/attachment/{id}', [AttachmentController::class, 'show']);


    Route::get('get/my/notification/{limit}', [NotificationController::class, 'index']);
    Route::get('get/my/unread/notification/{limit}', [NotificationController::class, 'unread']);
    Route::post('mark-as-read/notification/{id}', [NotificationController::class, 'markAsRead']);
    Route::delete('delete/read/notification/{id}', [NotificationController::class, 'destroy']);
    Route::delete('delete/all/read/notifications', [NotificationController::class, 'deleteAllRead']);
    Route::post('all/notifications/read', [NotificationController::class, 'allMarkSaRead']);
});
