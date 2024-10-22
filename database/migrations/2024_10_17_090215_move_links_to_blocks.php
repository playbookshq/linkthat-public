<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('links')->orderBy('id')->chunk(100, function ($links) {
            foreach ($links as $link) {
                DB::table('blocks')->insert([
                    'link_page_id' => $link->link_page_id,
                    'type' => 'link',
                    'data' => json_encode([
                        'url' => $link->url,
                        'title' => $link->title,
                        'description' => $link->description,
                        'icon' => $link->icon,
                        'type' => $link->type,
                    ]),
                    'order' => $link->order,
                    'is_visible' => $link->is_visible,
                    'created_at' => $link->created_at,
                    'updated_at' => $link->updated_at,
                ]);
            }
        });
    }

    public function down(): void
    {
        DB::table('blocks')->where('type', 'link')->delete();
    }
};
