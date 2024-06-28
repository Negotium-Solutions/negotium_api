<?php

namespace Database\Seeders\tenant;

use App\definitions\ModelTypeDefinitions;
use App\Models\Tenant\Activity;
use App\Models\Tenant\Form;
use App\Models\Tenant\Step;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $forms = ['Category',  'Process', 'Step', 'Activity'];

        foreach ($forms as $_form) {
            $form = new Form();
            $form->name = strtolower($_form);
            $form->created_at = now();
            $form->save();

            $step = new Step();
            $step->name = $_form;
            $step->parent_id = $form->id;
            $step->model_id = ModelTypeDefinitions::FORM;
            $step->save();
            $step->order = $step->id;
            $step->save();

            switch (strtolower($_form)) {
                case 'category':
                    Activity::insert([['name' => 'category', 'label' => 'Category', 'attributes' => '1', 'type_id' => 1, 'step_id' => $step->id]]);
                    break;
                case 'process':
                    Activity::insert([['name' => 'process', 'label' => 'Process', 'attributes' => '1', 'type_id' => 1, 'step_id' => $step->id]]);
                    Activity::insert([['name' => 'category', 'label' => 'Category', 'attributes' => '1', 'type_id' => 9, 'step_id' => $step->id]]);
                    break;
                case 'step':
                    Activity::insert([['name' => 'step', 'label' => 'Step', 'attributes' => '1', 'type_id' => 1, 'step_id' => $step->id]]);
                    Activity::insert([['name' => 'parent_id', 'label' => 'parent_id', 'attributes' => '1', 'type_id' => 4, 'step_id' => $step->id]]);
                    Activity::insert([['name' => 'model_id', 'label' => 'model_id', 'attributes' => '1', 'type_id' => 4, 'step_id' => $step->id]]);
                    break;
                case 'activity':
                    Activity::insert([['name' => 'name', 'label' => 'Name', 'attributes' => '1', 'type_id' => 1, 'step_id' => $step->id]]);
                    Activity::insert([['name' => 'label', 'label' => 'Label', 'attributes' => '1', 'type_id' => 1, 'step_id' => $step->id]]);
                    Activity::insert([['name' => 'guidance_note', 'label' => 'Guidance Note', 'attributes' => '1', 'type_id' => 2, 'step_id' => $step->id]]);
                    Activity::insert([['name' => 'attributes', 'label' => 'Attributes', 'attributes' => '1', 'type_id' => 1, 'step_id' => $step->id]]);
                    Activity::insert([['name' => 'type_id', 'label' => 'type_id', 'attributes' => '1', 'type_id' => 4, 'step_id' => $step->id]]);
                    Activity::insert([['name' => 'step_id', 'label' => 'step_id', 'attributes' => '1', 'type_id' => 4, 'step_id' => $step->id]]);
                    break;
            }
        }
    }
}
