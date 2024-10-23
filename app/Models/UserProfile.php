<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','full_name', 'bio', 'avatar_id', 'external_links'];

    protected $casts = [
        'external_links' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            // Automatically generate a UUID for the primary key 'id' if not set
            if (empty($user->id)) {
                $user->id = Str::uuid();
            }
        });
    }

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relationship with Upload for avatar
    public function avatar()
    {
        return $this->belongsTo(Upload::class, 'avatar_id');
    }
}
