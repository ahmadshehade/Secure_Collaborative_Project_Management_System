<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * Summary of table
     * @var string
     */
    protected $table = "projects";

    /**
     * Summary of fillable
     * @var array
     */
    protected $fillable = [
        'team_id',
        'name',
        'description',
        'status',
        'due_date'
    ];

    /**
     * Summary of guarded
     * @var array
     */
    protected $guarded = ['created_by_user_id'];


    /**
     * Summary of team
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Team, Project>
     */
    public function  team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    /**
     * Summary of userCreated
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Project>
     */
    public function userCreated()
    {
        return $this->belongsTo(User::class, 'created_by_user_id', 'id');
    }

    /**
     * Summary of name
     * @return Attribute
     */
    public function name(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucwords($value),
            set: fn($value) => strtolower($value)
        );
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
    /**
     * Summary of description
     * @return Attribute
     */
    public function description(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strip_tags(trim($value))
        );
    }

    /**
     * Summary of members
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User, Project, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public  function members()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }

    /**
     * Summary of scopeVisibleToUser
     * @param mixed $query
     * @param mixed $user
     */
    public function scopeVisibleToUser($query, $user)
    {
        return $query->withCount('members')->where(function ($q) use ($user) {
            $q->where('created_by_user_id', $user->id)
                ->orWhereHas('team', function ($q) use ($user) {
                    $q->where('owner_id', $user->id)
                        ->orWhereHas('members', function ($q) use ($user) {
                            $q->where('users.id', $user->id);
                        });
                })
                ->orWhereHas('members', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
        });
    }




    /**
     * Summary of tasks
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Task, Project>
     */
    public function  tasks()
    {
        return $this->hasMany(Task::class, 'project_id', 'id');
    }

    /**
     * Summary of comments
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Comment, Project>
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Summary of attachments
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Attachment, Project>
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
