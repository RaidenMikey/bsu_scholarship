<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'scholarship_id',
        'status',
    ];

    /**
     * Set default attribute values.
     */
    protected $attributes = [
        'status' => 'pending', // default if not provided
    ];

    /**
     * Casts for specific fields.
     */
    protected $casts = [
        'user_id' => 'integer',
        'scholarship_id' => 'integer',
    ];

    /**
     * Relationships
     */

    // Application belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Application belongs to a scholarship
    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }
}
