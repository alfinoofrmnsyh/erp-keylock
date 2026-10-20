<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!User::where('email', 'admin@keylock.co.id')->exists()) {
            User::factory()->create([
                'name' => 'Admin Keylock',
                'email' => 'admin@keylock.co.id',
            ]);
        }

        $this->call(MiniErpSeeder::class);
    }
}
