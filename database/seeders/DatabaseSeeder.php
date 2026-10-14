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
         User::updateOrCreate(
            [
                'name' => 'PortWise Admin',
                'email' => env('ADMIN_EMAIL'),
                'password_hash' => env('ADMIN_PASSWORD'),
            ]
        );

        $this->call([
            CategorySeeder::class,
        ]);
    }
}
