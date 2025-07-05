<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
        use HasFactory;
        protected $table = "attachments";

        /**
         * Summary of fillable
         * @var array
         */
        protected $fillable = [
                'path',
                'disk',
                'attachable_id',
                'attachable_type',
                'file_name',
                'file_size',
                'mime_type',
        ];

        /**
         * Summary of attachable
         * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, Attachment>
         */
        public function attachable()
        {
                return $this->morphTo();
        }
        /**
         * Summary of scopeVisibleToUser
         * @param mixed $query
         * @param \App\Models\User $user
         */
        public function scopeVisibleToUser($query, User $user)
        {
                return $query->where(function ($query) use ($user) {
                        $query->whereHasMorph(
                                'attachable',
                                [Project::class],
                                fn($q) => $q->whereHas('members', fn($q2) => $q2->where('users.id', $user->id))
                        )
                                ->orWhereHasMorph(
                                        'attachable',
                                        [Task::class],
                                        fn($q) => $q->whereHas('project.members', fn($q2) => $q2->where('users.id', $user->id))
                                )
                                ->orWhereHasMorph(
                                        'attachable',
                                        [Comment::class],
                                        fn($q) => $q->where('user_id', $user->id)
                                );
                });
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
