<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedUserRecord extends Model
{
    protected $fillable  = [
        'blocked_user_id',
        'user_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'blocked_user_id' => 'integer',
        'user_id' => 'integer'
    ];

    /**
     * BlockedUserRecord belongs to the blocked User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function blocked_user() // snake case, cause relation :)
    {
        return $this->belongsTo(User::class);
    }
}
