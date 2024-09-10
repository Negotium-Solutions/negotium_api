<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\Process;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant\Step>
 */
class StepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $process = Process::limit(2)->get();
        $process_id = $process[0]->id;
        return [
            'name' => fake()->word,
            'parent_id' => $process_id
        ];
    }
}
