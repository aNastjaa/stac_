<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Post;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'comment_text',
    ];

    // Set incrementing to false to use UUIDs
    public $incrementing = false;

    // Set the key type to string for UUIDs
    protected $keyType = 'string';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($comment) {
            // Generate a UUID for the primary key 'id' if it's not already set
            if (!$comment->id) {
                $comment->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Define the relationship to the User model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship to the Post model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
