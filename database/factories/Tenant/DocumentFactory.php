<?php

namespace Database\Factories\Tenant;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userIds = User::orderBy('id')->pluck('id')->toArray();
        $documentType = ['txt', 'pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

        return [
            'name' => fake()->name,
            'type' => $documentType[rand(1, 6)],
            'path' => "documents/",
            'size' => rand(1, 10000),
            'user_id' => $userIds[rand(0, count($userIds) - 1)],
            'profile_id' => rand(1, 22)
        ];
    }
}
