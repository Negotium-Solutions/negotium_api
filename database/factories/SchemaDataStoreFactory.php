<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchemaDataStore>
 */
class SchemaDataStoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $schema_id = 0;
        $data = [
            ["name" => "first_name", "type" => "string", "attributes" => ["required" => 1,"indent" => 0,"is_id" => 0,"is_passport" => 0]],
            ["name" => "last_name", "type" => "string", "attributes" => ["required" => 1,"indent" => 0,"is_id" => 0,"is_passport" => 0]],
            ["name" => "email", "type" => "string", "attributes" => ["required" => 1,"indent" => 0,"is_id" => 0,"is_passport" => 0]],
            ["name" => "age", "type" => "integer", "attributes" => ["required" => 0,"indent" => 0,"is_id" => 0,"is_passport" => 0]],
            ["name" => "biography", "type" => "text", "attributes" => ["required" => 1,"indent" => 0,"is_id" => 0,"is_passport" => 0]],
        ];

        return [
            'schema_id' => $schema_id + 1,
            'data' => json_encode($data)
        ];
    }
}
