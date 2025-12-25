<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles / permissions first
        $this->call(RolesAndPermissionsSeeder::class);

        // Initialize counters used for safe sequential numbering
        DB::table('counters')->updateOrInsert(
            ['key' => 'inspections.insp_number'],
            [
                'value' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Example user (optional)
        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
