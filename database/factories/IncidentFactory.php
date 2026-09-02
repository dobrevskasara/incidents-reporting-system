<?php

namespace Database\Factories;

use App\Models\Incident;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Incident>
 */
class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    public function definition(): array
    {
        $reportedAt = Carbon::instance(
            $this->faker->dateTimeBetween('-24 months', 'now')
        );

        return [
            'type' => $this->faker->randomElement(Incident::types()),
            'age_group' => $this->faker->randomElement(Incident::ageGroups()),
            'platform' => $this->faker->randomElement(Incident::platforms()),
            'status' => $this->faker->randomElement(Incident::statuses()),
            'description' => $this->faker->optional(0.4)->sentence(12),
            'reported_at' => $reportedAt,
            'created_at' => $reportedAt,
            'updated_at' => $reportedAt,
        ];
    }
}
