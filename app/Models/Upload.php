<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Upload extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'file_url',
        'file_type',
    ];

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "booted" method of the model.
     *
     * This is used to generate a UUID for the primary key 'id'
     * before creating a new Upload instance.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function ($upload) {
            if (!$upload->id) {
                $upload->id = (string) Str::uuid();
            }
        });

        static::retrieved(function ($upload) {
           
            $upload->file_url = Storage::url($upload->file_url);
        });
    }

    /**
     * Get the user profile associated with the upload.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // public function userProfile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    // {
    //     return $this->belongsTo(UserProfile::class);
    // }
}
