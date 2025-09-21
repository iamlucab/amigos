<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'birthday' => $this->faker->date('Y-m-d', '-18 years'),
            'mobile_number' => $this->faker->unique()->numerify('09#########'),
            'occupation' => $this->faker->jobTitle,
            'address' => $this->faker->address,
            'photo' => null,
            'role' => $this->faker->randomElement(['Admin', 'Member']),
            'sponsor_id' => null, // temporarily null; we'll assign later
            'voter_id' => null,
        ];
    }
}
