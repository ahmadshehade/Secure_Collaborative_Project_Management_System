<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    /**
     * Summary of table
     * @var string
     */
    protected $table = "tasks";

    /**
     * Summary of fillable
     * @var array
     */
    protected $fillable = [
        'project_id',
        'assigned_to_user_id',
        'name',
        'description',
        'status',
        'priority',
        'due_date'
    ];

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
     * Summary of getCreatedAtFormattedAttribute
     */
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at?->format('Y-m-d H:i');
    }
    /**
     * Summary of getUpdatedAtFormattedAttribute
     */
    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at?->format('Y-m-d H:i');
    }

    /**
     * Summary of project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Project, Task>
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
    /**
     * Summary of user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Task>
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id', 'id');
    }

    /**
     * Summary of setDueDateAttribute
     * @param mixed $value
     * @return void
     */
    public function setDueDateAttribute($value)
    {
        try {
            $this->attributes['due_date'] = Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            $this->attributes['due_date'] = null;
        }
    }


    /**
     * Summary of scopeVisibleToUser
     * @param mixed $query
     * @param mixed $user
     */
    public function scopeVisibleToUser($query, $user)
    {
        return $query->whereHas('project.members', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->with(['project', 'user'])->latest();
    }

    /**
     * Summary of comments
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Comment, Task>
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Summary of attachments
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Attachment, Task>
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Summary of description
     * @return CastsAttribute
     */
    public function description()
    {
        return CastsAttribute::make(
            set: fn($value) => strip_tags(trim($value))
        );
    }
}
