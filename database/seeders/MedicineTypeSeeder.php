<?php

namespace Database\Seeders;

use App\Models\MedicineType;
use Illuminate\Database\Seeder;

class MedicineTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'ANALGESIC',
            'MUSCLE RELAXANT',
            'ANTIPYRETIC',
            'MUCOLYTIC',
            'DECONGESTANT',
            'ANTITUSSIVE',
            'ANTI-HYPERTENSION',
            'CORONARY DILATOR',
            'ANTIVERTIGO',
            'ANTIBIOTIC',
            'ANTISPASMODIC',
            'GASTROKINETIC/ANTIEMETIC',
            'ANTIMOTILITY',
            'ELECTROLYTE ORAL',
            'ANTACID/ANTIFLATULENT',
            'PROTON PUMP INHIBITOR',
            'ANTIHISTAMINE',
            'ANTI-ASTHMA',
            'IV SET',
            'TOPICAL OINTMENT/GEL/LOTION',
            'EYE / EAR DROPS',
        ];

        foreach ($types as $name) {
            MedicineType::updateOrCreate(['name' => $name], ['name' => $name]);
        }
    }
}
