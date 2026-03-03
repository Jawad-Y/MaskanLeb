<?php

namespace Database\Seeders;

use App\Models\Judiciary;
use Illuminate\Database\Seeder;

class JudiciarySeeder extends Seeder
{
    public function run(): void
    {
        $judiciaries = [
            ['name' => 'Baabda', 'name_ar' => 'بعبدا'],
            ['name' => 'Metn', 'name_ar' => 'المتن'],
            ['name' => 'Keserwan', 'name_ar' => 'كسروان'],
            ['name' => 'Tripoli', 'name_ar' => 'طرابلس'],
            ['name' => 'Zahle', 'name_ar' => 'زحلة'],
            ['name' => 'Tyre', 'name_ar' => 'صور'],
            ['name' => 'Sidon', 'name_ar' => 'صيدا'],
            ['name' => 'Aley', 'name_ar' => 'عاليه'],
            ['name' => 'Batroun', 'name_ar' => 'البترون'],
            ['name' => 'Jbeil', 'name_ar' => 'جبيل'],
            ['name' => 'Chouf', 'name_ar' => 'الشوف'],
            ['name' => 'Nabatieh', 'name_ar' => 'النبطية'],
            ['name' => 'Bint Jbeil', 'name_ar' => 'بنت جبيل'],
            ['name' => 'Marjayoun', 'name_ar' => 'مرجعيون'],
            ['name' => 'Hasbaya', 'name_ar' => 'حاصبيا'],
            ['name' => 'West Bekaa', 'name_ar' => 'البقاع الغربي'],
            ['name' => 'Rashaya', 'name_ar' => 'راشيا'],
            ['name' => 'Baalbek', 'name_ar' => 'بعلبك'],
            ['name' => 'Hermel', 'name_ar' => 'الهرمل'],
            ['name' => 'Zgharta', 'name_ar' => 'زغرتا'],
            ['name' => 'Koura', 'name_ar' => 'الكورة'],
            ['name' => 'Bsharri', 'name_ar' => 'بشري'],
            ['name' => 'Miniyeh-Danniyeh', 'name_ar' => 'المنية-الضنية'],
            ['name' => 'Akkar', 'name_ar' => 'عكار'],
            ['name' => 'Beirut', 'name_ar' => 'بيروت'],
            ['name' => 'Jezzine', 'name_ar' => 'جزين'],
        ];

        foreach ($judiciaries as $judiciary) {
            Judiciary::updateOrCreate(
                ['name' => $judiciary['name']],
                $judiciary,
            );
        }
    }
}
