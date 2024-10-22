<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_page_id',
        'type',
        'data',
        'order',
        'is_visible',
    ];

    protected $casts = [
        'data' => 'array',
        'is_visible' => 'boolean',
    ];

    public function linkPage(): BelongsTo
    {
        return $this->belongsTo(LinkPage::class);
    }
}
