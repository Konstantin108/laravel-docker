<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'telegram' => '@'.$this->faker->userName(),
        ];
    }

    public function user(int $userId): ContactFactory
    {
        return $this->state(fn (): array => [
            'user_id' => $userId,
        ]);
    }
}
