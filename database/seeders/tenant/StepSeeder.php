<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Step;
use Illuminate\Database\Seeder;

class StepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Step::insert([
           ['name' => 'Step 01', 'parent_id' => 1, 'model_id' => 1],
           ['name' => 'Step 02', 'parent_id' => 1, 'model_id' => 1],
           ['name' => 'Step 03', 'parent_id' => 1, 'model_id' => 1],
           ['name' => 'Step 04', 'parent_id' => 1, 'model_id' => 1],
           ['name' => 'Step 05', 'parent_id' => 2, 'model_id' => 1],
           ['name' => 'Step 06', 'parent_id' => 3, 'model_id' => 1],
           ['name' => 'Step 07', 'parent_id' => 4, 'model_id' => 1],
           ['name' => 'Step 08', 'parent_id' => 3, 'model_id' => 1],
           ['name' => 'Step 09', 'parent_id' => 4, 'model_id' => 1],
           ['name' => 'Step 10', 'parent_id' => 5, 'model_id' => 1],
           ['name' => 'Step 11', 'parent_id' => 6, 'model_id' => 1],
           ['name' => 'Step 12', 'parent_id' => 7, 'model_id' => 1],
           ['name' => 'Step 13', 'parent_id' => 8, 'model_id' => 1],
           ['name' => 'Step 14', 'parent_id' => 9, 'model_id' => 1],
           ['name' => 'Step 15', 'parent_id' => 21, 'model_id' => 1],
           ['name' => 'Step 16', 'parent_id' => 22, 'model_id' => 1],
           ['name' => 'Step 17', 'parent_id' => 25, 'model_id' => 1],
           ['name' => 'Step 18', 'parent_id' => 1, 'model_id' => 1],
           ['name' => 'Step 19', 'parent_id' => 1, 'model_id' => 1],
           ['name' => 'Step 20', 'parent_id' => 1, 'model_id' => 1],
           ['name' => 'Step 21', 'parent_id' => 2, 'model_id' => 1],
           ['name' => 'Step 22', 'parent_id' => 2, 'model_id' => 1],
           ['name' => 'Step 23', 'parent_id' => 3, 'model_id' => 1],
           ['name' => 'Step 24', 'parent_id' => 3, 'model_id' => 1],
           ['name' => 'Step 25', 'parent_id' => 4, 'model_id' => 1],
           ['name' => 'Step 26', 'parent_id' => 4, 'model_id' => 1],
           ['name' => 'Step 27', 'parent_id' => 5, 'model_id' => 1],
           ['name' => 'Step 28', 'parent_id' => 5, 'model_id' => 1],
           ['name' => 'Step 29', 'parent_id' => 6, 'model_id' => 1],
           ['name' => 'Step 30', 'parent_id' => 6, 'model_id' => 1],
           ['name' => 'Step 31', 'parent_id' => 6, 'model_id' => 1],
           ['name' => 'Step 32', 'parent_id' => 6, 'model_id' => 1],
           ['name' => 'Step 33', 'parent_id' => 7, 'model_id' => 1],
           ['name' => 'Step 34', 'parent_id' => 7, 'model_id' => 1],
           ['name' => 'Step 35', 'parent_id' => 7, 'model_id' => 1],
           ['name' => 'Step 36', 'parent_id' => 7, 'model_id' => 1],
           ['name' => 'Step 37', 'parent_id' => 7, 'model_id' => 1],
           ['name' => 'Step 38', 'parent_id' => 7, 'model_id' => 1],
           ['name' => 'Step 39', 'parent_id' => 8, 'model_id' => 1],
           ['name' => 'Step 40', 'parent_id' => 8, 'model_id' => 1],
           ['name' => 'Step 41', 'parent_id' => 8, 'model_id' => 1],
           ['name' => 'Step 42', 'parent_id' => 8, 'model_id' => 1],
           ['name' => 'Step 43', 'parent_id' => 8, 'model_id' => 1],
           ['name' => 'Step 44', 'parent_id' => 9, 'model_id' => 1],
           ['name' => 'Step 45', 'parent_id' => 9, 'model_id' => 1],
           ['name' => 'Step 46', 'parent_id' => 9, 'model_id' => 1],
           ['name' => 'Step 47', 'parent_id' => 11, 'model_id' => 1],
           ['name' => 'Step 48', 'parent_id' => 12, 'model_id' => 1],
           ['name' => 'Step 49', 'parent_id' => 13, 'model_id' => 1],
           ['name' => 'Step 50', 'parent_id' => 14, 'model_id' => 1]
        ]);
    }
}
