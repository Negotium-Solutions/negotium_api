<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\Schema as TenantSchema;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Session;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant\Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userIds = User::orderBy('id')->pluck('id')->toArray();
        $tenantSchema = TenantSchema::where('dynamic_model_type_id', 1)->pluck('id')->toArray();

        Session::put('schema_id', $tenantSchema[rand(0, 1)]);
        $profileIds = DynamicModel::pluck('id')->toArray();

        return [
            'subject' => fake()->realTextBetween(5, 15),
            'note' => fake()->realText,
            'reminder_datetime' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'profile_id' => $profileIds[rand(0, count($profileIds) - 1)],
            'user_id' => $userIds[rand(0, count($userIds) - 1)]
        ];
    }
}
