<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserProfile
 *
 * Represents a user's profile with personal details and external links.
 *
 * @package App\Models
 * @property string $id
 * @property string $user_id
 * @property string|null $full_name
 * @property string|null $bio
 * @property string|null $avatar_id
 * @property array|null $external_links
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Upload|null $avatar
 */
class UserProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'full_name',
        'bio',
        'avatar_id',
        'external_links'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'external_links' => 'array',
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Boot method to automatically generate a UUID for the primary key.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function (UserProfile $user) {
            if (empty($user->id)) {
                $user->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the user that owns the profile.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the avatar associated with the profile.
     *
     * @return BelongsTo
     */
    public function avatar(): BelongsTo
    {
        return $this->belongsTo(Upload::class, 'avatar_id');
    }
}
