<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
    protected static function booted()
    {
        static::creating(function ($comment) {
            // Generate a UUID for the primary key 'id' if it's not already set
            if (empty($comment->id)) {
                $comment->id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
