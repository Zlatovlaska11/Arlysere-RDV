<?php

namespace Database\Seeders;

use App\Models\Option;
use Illuminate\Database\Seeder;

class OptionsSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'theme' => [
                ['value' => 'Logement', 'color' => '#3b82f6'],
                ['value' => 'Santé', 'color' => '#22c55e'],
                ['value' => 'Emploi', 'color' => '#f97316'],
                ['value' => 'Administratif', 'color' => '#8b5cf6'],
                ['value' => 'Écoute', 'color' => '#ec4899'],
                ['value' => 'Justice', 'color' => '#ef4444'],
                ['value' => 'Mobilité', 'color' => '#14b8a6'],
                ['value' => 'Alimentation', 'color' => '#eab308'],
            ],
            'difficulty' => [
                ['value' => 'Autonome', 'color' => '#22c55e'],
                ['value' => 'Peu autonome', 'color' => '#f97316'],
                ['value' => 'Non autonome', 'color' => '#ef4444'],
            ],
            'age' => [
                ['value' => '-12'],
                ['value' => '12-17'],
                ['value' => '18-25'],
                ['value' => '26-40'],
                ['value' => '41-60'],
                ['value' => '60+'],
            ],
            'statut' => [
                ['value' => 'Hébergé'],
                ['value' => 'Sans abri'],
                ['value' => 'En logement'],
                ['value' => 'En structure'],
            ],
            'location' => [
                ['value' => 'Rue'],
                ['value' => 'Centre-ville'],
                ['value' => 'Périphérie'],
                ['value' => 'Domicile'],
                ['value' => 'Structure partenaire'],
            ],
            'commune' => [
                ['value' => 'Amiens'],
                ['value' => 'Abbeville'],
                ['value' => 'Péronne'],
                ['value' => 'Albert'],
                ['value' => 'Doullens'],
                ['value' => 'Montdidier'],
                ['value' => 'Ham'],
                ['value' => 'Roye'],
            ],
        ];

        foreach ($data as $type => $options) {
            foreach ($options as $index => $option) {
                Option::create([
                    'type' => $type,
                    'value' => $option['value'],
                    'color' => $option['color'] ?? null,
                    'sort_order' => $index,
                    'is_active' => true,
                ]);
            }
        }
    }
}
