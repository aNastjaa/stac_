<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Upload extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_url',
        'file_type',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    // // Optionally, define the relationship with the UserProfile if needed
    // public function userProfile()
    // {
    //     return $this->belongsTo(UserProfile::class);
    // }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($upload) {
            // Automatically generate a UUID for the primary key 'id' if not set
            if (empty($upload->id)) {
                $upload->id = (string) Str::uuid(); // Generate UUID as a string
            }
        });
    }
}
