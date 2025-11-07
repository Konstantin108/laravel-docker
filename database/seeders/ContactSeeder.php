<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    private const CONTACTS_COUNT = 30;

    public function run(): void
    {
        $users = User::all();

        if ($users->count() < self::CONTACTS_COUNT) {
            $users->merge(
                User::factory()
                    ->count(self::CONTACTS_COUNT - $users->count())
                    ->create()
            );
        }

        // TODO kpstya возможно переделать сидеры

        $users->where(static fn (User $user): bool => $user->contact === null)
            ->each(static fn (User $user): Contact => Contact::factory()
                ->user($user->id)
                ->create());
    }
}
