<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_page_id',
        'type',
        'url',
        'title',
        'description',
        'icon',
        'order',
        'is_visible',
    ];

    public function linkPage(): BelongsTo
    {
        return $this->belongsTo(LinkPage::class);
    }
}
