<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Archive extends Model
{
    use HasFactory;

    protected $table = 'archives';

    protected $fillable = [
        'post_id',
        'moved_at',
        'theme',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    // Automatically generate a UUID for the primary key
    protected static function booted()
    {
        static::creating(function ($archive) {
            $archive->id = (string) Str::uuid();
        });
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
