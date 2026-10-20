<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        \App\Models\Post::create([
            'slug' => 'halo-dunia',
            'lang' => 'id',
            'title' => 'Halo Dunia',
            'content' => 'Ini adalah konten pertama dari Laravel.',
            'status' => 'published',
        ]);

        \App\Models\Post::create([
            'slug' => 'hello-world',
            'lang' => 'en',
            'title' => 'Hello World',
            'content' => 'This is the first content from Laravel.',
            'status' => 'published',
        ]);
    }
}
