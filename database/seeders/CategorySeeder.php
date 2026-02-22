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
            ['code' => 'B', 'name' => 'GI Disorder'],
            ['code' => 'C', 'name' => 'Musculo-Skeletal Disorder'],
            ['code' => 'D', 'name' => 'BP Monitoring'],
            ['code' => 'E', 'name' => 'Cardio Disorder'],
        ];

        foreach ($categories as $cat) {
            // Ito ay mag-che-check kung may record na, kung wala pa, gagawa siya.
            Category::updateOrCreate(['code' => $cat['code']], $cat);
        }
    }
}