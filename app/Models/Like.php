<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
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
        static::creating(function ($like) {
            // Generate a UUID for the primary key 'id' if it's not already set
            if (!$like->id) {
                $like->id = (string) Str::uuid();
            }
        });
    }
}
