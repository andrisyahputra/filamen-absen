<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    use HasFactory;

    // protected $fillable = ['user_id', 'shift_id', 'office_id', 'is_wfa'];
    // memastikan boolean
    protected $casts = [
        'is_wfa'=> 'boolean',
        'is_banned'=> 'boolean',
    ];
    protected $guarded = ['id'];

    /**
     * Get the user that owns the Jadwal
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }
}
