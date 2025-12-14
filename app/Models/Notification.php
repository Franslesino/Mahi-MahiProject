<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->read_at = now();
        $this->save();
    }

    public function isUnread()
    {
        return is_null($this->read_at);
    }
}
