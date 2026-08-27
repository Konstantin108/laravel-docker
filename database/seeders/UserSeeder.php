<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    private const USERS_COUNT = 40;

    public function run(): void
    {
        User::factory()
            ->count(self::USERS_COUNT)
            ->hasContact()
            ->create();
    }
}
