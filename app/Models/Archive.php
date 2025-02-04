<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Post;
use App\Models\Theme;

class Archive extends Model
{
    use HasFactory;

    protected $table = 'archives';

    protected $fillable = [
        'post_id',
        'theme_id',
        'theme_name',
        'moved_at',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Automatically generate a UUID for the primary key
     */
    protected static function booted(): void
    {
        static::creating(function ($archive) {
            $archive->id = (string) Str::uuid();
        });
    }

    /**
     * Define the relationship to the Post model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * Define the relationship to the Theme model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function theme(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Theme::class, 'theme_id');
    }
}
