<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'A', 'name' => 'Respiratory Disorder'],
            ['code' => 'B', 'name' => 'GI-L Disorders'],
            ['code' => 'C', 'name' => 'Musculo-Skeletal Disorder'],
            ['code' => 'D', 'name' => 'BP Monitoring'],
            ['code' => 'E', 'name' => 'Cardiovascular Disorder'],
            ['code' => 'F', 'name' => 'CNS Disorder'],
            ['code' => 'G', 'name' => 'Immune System Disorder'],
            ['code' => 'H', 'name' => 'Derma Disorder'],
            ['code' => 'I', 'name' => 'Surgery / Trauma'],
            ['code' => 'J', 'name' => 'E.E.E.N.T Disorders'],
            ['code' => 'K', 'name' => 'Reproductive Disorders'],
            ['code' => 'L', 'name' => 'Nutritional Deficiency'],
            ['code' => 'M', 'name' => 'Endocrine Disorders'],
            ['code' => 'N', 'name' => 'Urinary Disorders'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['code' => $cat['code']], $cat);
        }
    }
}
