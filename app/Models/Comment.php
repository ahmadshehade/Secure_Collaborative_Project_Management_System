<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
        use HasFactory;
        protected $table = "Comments";

        /**
         * Summary of fillable
         * @var array
         */
        protected $fillable = ['commentable_id', 'commentable_type', 'content'];

        /**
         * Summary of guarded
         * @var array
         */
        protected  $guarded = ['user_id'];

        /**
         * Summary of commentable
         * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, Comment>
         */
        public function  commentable()
        {
                return $this->morphTo();
        }

        /**
         * Summary of user
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Comment>
         */
        public function user()
        {
                return $this->belongsTo(User::class, 'user_id', 'id');
        }

        /**
         * Summary of attachments
         * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Attachment, Comment>
         */
        public function attachments()
        {
                return $this->morphMany(Attachment::class, 'attachable');
        }

        /**
         * Summary of latestAttachment
         * @return \Illuminate\Database\Eloquent\Relations\MorphOne<Attachment, Comment>
         */
        public function latestAttachment()
        {
                return $this->morphOne(Attachment::class, 'attachable')
                        ->ofMany('created_at', 'max');
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
