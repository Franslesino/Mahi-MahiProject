<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas notifikasi.
 */
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

    /**
     * Relasi belongsTo ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Menangani logika model.
     */
    public function markAsRead()
    {
        $this->read_at = now();
        $this->save();
    }

    /**
     * Memeriksa unread.
     */
    public function isUnread()
    {
        return is_null($this->read_at);
    }
}
