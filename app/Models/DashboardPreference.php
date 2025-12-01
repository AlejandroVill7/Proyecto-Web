<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'widget_key',
        'position',
        'is_visible',
        'settings',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'settings' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
