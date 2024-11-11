<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\DynamicModel;
use App\Models\Tenant\Profile;
use App\Models\Tenant\Schema as TenantSchema;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Session;

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

        $tenantSchema = TenantSchema::where('dynamic_model_type_id', 1)->pluck('id')->toArray();

        Session::put('schema_id', $tenantSchema[rand(0, 1)]);
        $profileIds = DynamicModel::pluck('id')->toArray();

        return [
            'name' => fake()->name,
            'type' => $documentType[rand(1, 6)],
            'path' => "documents/",
            'size' => rand(1, 10000),
            'user_id' => $userIds[rand(0, count($userIds) - 1)],
            'profile_id' => $profileIds[rand(0, count($profileIds) - 1)],
        ];
    }
}
