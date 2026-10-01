<?php

namespace Database\Factories;

use App\Enums\ContactType;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => ContactType::Customer,
            'name' => fake()->company(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }

    public function customer(): static
    {
        return $this->state(fn () => ['type' => ContactType::Customer]);
    }

    public function supplier(): static
    {
        return $this->state(fn () => ['type' => ContactType::Supplier]);
    }
}
