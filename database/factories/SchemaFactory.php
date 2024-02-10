<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schema>
 */
class SchemaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $columns = [
            ["name" => "first_name", "type" => "string", "attributes" => ["required" => 1,"indent" => 0,"is_id" => 0,"is_passport" => 0]],
            ["name" => "last_name", "type" => "string", "attributes" => ["required" => 1,"indent" => 0,"is_id" => 0,"is_passport" => 0]],
            ["name" => "email", "type" => "string", "attributes" => ["required" => 1,"indent" => 0,"is_id" => 0,"is_passport" => 0]],
            ["name" => "age", "type" => "integer", "attributes" => ["required" => 0,"indent" => 0,"is_id" => 0,"is_passport" => 0]],
        ];

        return [
            'name' => fake()->name,
            'columns' => json_encode($columns)
        ];
    }
}
