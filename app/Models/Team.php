<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
  use HasFactory;
  /**
   * Summary of fillable
   * @var array
   */
  protected $fillable = ['name', 'owner_id'];

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
   * Summary of name
   * @return Attribute
   */
  public function name(): Attribute
  {
    return Attribute::make(
      get: fn($value) => ucwords($value),
      set: fn($value) => ucwords(strtolower($value)),
    );
  }
  /**
   * Summary of owner
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Team>
   */
  public function  owner()
  {
    return $this->belongsTo(User::class, 'owner_id', 'id');
  }

  /**
   * Summary of members
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User, Team, \Illuminate\Database\Eloquent\Relations\Pivot>
   */
  public function members()
  {
    return $this->belongsToMany(User::class, 'team_user');
  }
  /**
   * Summary of projects
   * @return \Illuminate\Database\Eloquent\Relations\HasMany<Project, Team>
   */
  public  function  projects()
  {
    return $this->hasMany(Project::class, 'team_id', 'id');
  }

  /**
   * Summary of scopeVisibleToUser
   * @param mixed $query
   * @param mixed $user
   */
  public function scopeVisibleToUser($query, $user)
  {
    return $query->where('owner_id', $user->id)
      ->orWhereHas('members', fn($q) => $q->where('users.id', $user->id));
  }
}
