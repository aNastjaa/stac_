<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'theme_id',
        'image_url',
        'description',
    ];

    protected $keyType = 'string'; // Set the key type to string
    public $incrementing = false;   // Disable auto-incrementing

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($post) {
            // UUID for the primary key 'id'
            if (empty($post->id)) {
                $post->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the user that owns the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the theme associated with the post.
     */
    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

     /**
     * Get the comments associated with the post.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the like associated with the post.
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    /**
     * Move post to archive.
     */
    public function archive()
    {
        return $this->hasOne(Archive::class);
    }

}
