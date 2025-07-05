<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',

    ];
    protected $guarded = ['role'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ownedTeams()
    {
        return $this->hasMany(Team::class, 'owner_id', 'id');
    }

    /**
     * Summary of teams
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Team, User, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_user');
    }
    /**
     * Summary of projects
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Project, User>
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'created_by_user_id', 'id');
    }
    /**
     * Summary of memberProjects
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Project, User, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function memberProjects()
    {

        return $this->belongsToMany(Project::class, 'project_user');
    }
    /**
     * Summary of tasks
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Task, User>
     */
    public function  tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to_user_id', 'id');
    }
    /**
     * Summary of comments
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Comment, User>
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }


    /**
     * Summary of getCreatedAtAttribute
     * @param mixed $value
     * @return string|null
     */
    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d H:i') : null;
    }

    /**
     * Summary of getUpdatedAtAttribute
     * @param mixed $value
     * @return string|null
     */
    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d H:i') : null;
    }
}
