<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LinkPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'title',
        'description',
        'theme',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }
}
