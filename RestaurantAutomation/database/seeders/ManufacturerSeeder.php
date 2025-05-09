<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Manufacturer;
use Illuminate\Support\Facades\File;

class ManufacturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('seeders/data/manufacturers.json'));
        $manufacturers = json_decode($json, true)['manufacturers'];

        foreach ($manufacturers as $manufacturerData) {
            // Eğer üretici zaten varsa güncelle, yoksa yeni ekle
            Manufacturer::updateOrCreate(
                ['name' => $manufacturerData['name']],
                $manufacturerData
            );
        }
    }
} 