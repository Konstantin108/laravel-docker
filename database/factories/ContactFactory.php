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
    protected $model = Contact::class;

    // TODO kpstya проверить, что в фабриках заполняются все поля моделей

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

    public function withEmail(string $email): self
    {
        return $this->state(fn (): array => ['email' => $email]);
    }

    public function withPhone(string $phone): self
    {
        return $this->state(fn (): array => ['phone' => $phone]);
    }

    public function withTelegram(string $telegram): self
    {
        return $this->state(fn (): array => ['telegram' => $telegram]);
    }
}
