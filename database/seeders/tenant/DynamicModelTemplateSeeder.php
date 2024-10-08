<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\DynamicModelTemplate;
use Illuminate\Database\Seeder;

class DynamicModelTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DynamicModelTemplate::insert([
            ['name' => 'Personal Information', 'sample' => 'First Name, Surname, Nickname, ID Number', 'file' => 'database/templates/profile/personal_information.json', 'dynamic_model_category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Contact Information', 'sample' => 'Mobile Number, Email Address, Work Number, Home Number', 'file' => 'database/templates/profile/contact_information.json', 'dynamic_model_category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Home Address', 'sample' => 'Residence Type, Street Address, Suburb, City, Country, Postal Code', 'file' => 'database/templates/profile/home_address.json', 'dynamic_model_category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Work Address', 'sample' => 'Residence Type, Street Address, Suburb, City, Country, Postal Code', 'file' => 'database/templates/profile/work_address.json', 'dynamic_model_category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bank Details', 'sample' => 'Account Holder, Account Type, Bank, Account Number', 'file' => 'database/templates/profile/bank_details.json', 'dynamic_model_category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tax Details', 'sample' => 'Tax Category, Tax Number', 'file' => 'database/templates/profile/tax_details.json', 'dynamic_model_category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Company Information', 'sample' => 'Company Name, Company Registration', 'file' => 'database/templates/profile/company_information.json', 'dynamic_model_category_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Company Address', 'sample' => 'Residence Type, Street Address, Suburb, City, Country, Postal Code', 'file' => 'database/templates/profile/company_address.json', 'dynamic_model_category_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
