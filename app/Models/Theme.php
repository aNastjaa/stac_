<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'theme_name',
        'start_date',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($theme) {
            // UUID for the primary key 'id'
            if (empty($theme->id)) {
                $theme->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the posts associated with the theme.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
