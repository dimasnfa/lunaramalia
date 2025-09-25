<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RekomendasiMenu;
use App\Models\Menu;
use Carbon\Carbon;

class RekomendasiMenuSeeder extends Seeder
{
    public function run()
    {
        // Hapus data lama
        RekomendasiMenu::truncate();

        // Data rekomendasi berdasarkan analisis cross-category yang logis dengan metrics Apriori
        $rekomendasiData = [
            // MAKANAN -> rekomendasi ke kategori lain
            'Nasi' => [
                'recommended' => ['Ayam Goreng', 'Teh Manis Dingin', 'Nugget'],
                'confidence' => 85.5,
                'support' => 0.72,
                'lift' => 2.3,
                'frequency_count' => 145
            ],
            'Ayam Goreng' => [
                'recommended' => ['Nasi', 'Sambal Geprek', 'Teh Manis Dingin'],
                'confidence' => 92.3,
                'support' => 0.78,
                'lift' => 2.8,
                'frequency_count' => 167
            ],
            'Ayam Bakar' => [
                'recommended' => ['Nasi', 'Sambal Dabu Dabu', 'Coffe Ekspresso'],
                'confidence' => 88.7,
                'support' => 0.65,
                'lift' => 2.1,
                'frequency_count' => 123
            ],
            'Ayam Mentega' => [
                'recommended' => ['Nasi', 'Sambal Bawang', 'Cappucino Ice'],
                'confidence' => 79.4,
                'support' => 0.58,
                'lift' => 1.9,
                'frequency_count' => 98
            ],
            'Ayam Lada Hitam' => [
                'recommended' => ['Nasi', 'Sambal Terasi', 'Jeruk Dingin'],
                'confidence' => 81.2,
                'support' => 0.61,
                'lift' => 2.0,
                'frequency_count' => 104
            ],
            'Ayam Lombok Ijo' => [
                'recommended' => ['Nasi', 'Sambal Ijo', 'Teh Tubruk'],
                'confidence' => 76.8,
                'support' => 0.54,
                'lift' => 1.8,
                'frequency_count' => 87
            ],
            'Ayam Asam Manis' => [
                'recommended' => ['Nasi', 'Sambal Mentah', 'Jus Jeruk'],
                'confidence' => 83.6,
                'support' => 0.63,
                'lift' => 2.2,
                'frequency_count' => 112
            ],
            'Ayam Saos Padang' => [
                'recommended' => ['Nasi', 'Sambal Pecak', 'Teh Manis Panas'],
                'confidence' => 77.9,
                'support' => 0.56,
                'lift' => 1.9,
                'frequency_count' => 93
            ],
            'Ayam Rica Rica' => [
                'recommended' => ['Nasi', 'Sambal Dabu Dabu', 'Jeruk Panas'],
                'confidence' => 84.3,
                'support' => 0.67,
                'lift' => 2.4,
                'frequency_count' => 119
            ],
            'Sop Ayam' => [
                'recommended' => ['Nasi', 'Keong Racun', 'Teh Tubruk Susu'],
                'confidence' => 72.1,
                'support' => 0.49,
                'lift' => 1.6,
                'frequency_count' => 78
            ],
            'Garang Asem Ayam' => [
                'recommended' => ['Nasi', 'French Fries', 'Jeruk Dingin'],
                'confidence' => 69.5,
                'support' => 0.45,
                'lift' => 1.5,
                'frequency_count' => 68
            ],
            'Sambal Mentah' => [
                'recommended' => ['Ayam Goreng', 'Nasi Goreng Ayam', 'Teh Manis Dingin'],
                'confidence' => 86.2,
                'support' => 0.71,
                'lift' => 2.5,
                'frequency_count' => 134
            ],
            'Sambal Pecak' => [
                'recommended' => ['Ayam Saos Padang', 'Mie Goreng', 'Coffe Ekspresso'],
                'confidence' => 74.8,
                'support' => 0.52,
                'lift' => 1.7,
                'frequency_count' => 82
            ],
            'Sambal Terasi' => [
                'recommended' => ['Ayam Lada Hitam', 'Indomie Rebus', 'Teh Tubruk'],
                'confidence' => 78.3,
                'support' => 0.57,
                'lift' => 1.8,
                'frequency_count' => 91
            ],
            'Sambal Geprek' => [
                'recommended' => ['Ayam Goreng', 'Nugget', 'Teh Manis Dingin'],
                'confidence' => 89.7,
                'support' => 0.75,
                'lift' => 2.7,
                'frequency_count' => 156
            ],
            'Sambal Bawang' => [
                'recommended' => ['Ayam Mentega', 'Nasi Goreng Gemilang', 'Cappucino Hot'],
                'confidence' => 75.6,
                'support' => 0.53,
                'lift' => 1.7,
                'frequency_count' => 85
            ],
            'Sambal Ijo' => [
                'recommended' => ['Ayam Lombok Ijo', 'Kwetiau Goreng', 'Jeruk Panas'],
                'confidence' => 73.2,
                'support' => 0.50,
                'lift' => 1.6,
                'frequency_count' => 79
            ],
            'Sambal Dabu Dabu' => [
                'recommended' => ['Ayam Rica Rica', 'French Fries', 'Jus Tomat'],
                'confidence' => 80.9,
                'support' => 0.62,
                'lift' => 2.1,
                'frequency_count' => 108
            ],

            // MINUMAN -> rekomendasi ke kategori lain
            'Jus Alpukat' => [
                'recommended' => ['Roti Bakar', 'Ayam Mentega', 'Nasi Goreng Gemilang'],
                'confidence' => 68.4,
                'support' => 0.43,
                'lift' => 1.4,
                'frequency_count' => 63
            ],
            'Jus Apel' => [
                'recommended' => ['Pisang Bakar', 'Sop Ayam', 'Mie Goreng'],
                'confidence' => 71.7,
                'support' => 0.48,
                'lift' => 1.5,
                'frequency_count' => 74
            ],
            'Jus Strawberry' => [
                'recommended' => ['Roti Bakar Keju Coklat', 'Ayam Asam Manis', 'Indomie Goreng toping'],
                'confidence' => 66.9,
                'support' => 0.41,
                'lift' => 1.3,
                'frequency_count' => 58
            ],
            'Jus Jeruk' => [
                'recommended' => ['Ayam Asam Manis', 'French Fries', 'Nasi Goreng Seafood'],
                'confidence' => 82.5,
                'support' => 0.64,
                'lift' => 2.2,
                'frequency_count' => 115
            ],
            'Jus Tomat' => [
                'recommended' => ['Sambal Dabu Dabu', 'Sosis Goreng', 'Kwetiau Rebus'],
                'confidence' => 70.3,
                'support' => 0.47,
                'lift' => 1.5,
                'frequency_count' => 71
            ],
            'Jus Mangga' => [
                'recommended' => ['Nugget', 'Ayam Bakar', 'Nasi Goreng Ayam'],
                'confidence' => 76.1,
                'support' => 0.55,
                'lift' => 1.8,
                'frequency_count' => 88
            ],
            'Jus Melon' => [
                'recommended' => ['Keong Racun', 'Garang Asem Ayam', 'Mie Goreng'],
                'confidence' => 65.8,
                'support' => 0.40,
                'lift' => 1.2,
                'frequency_count' => 54
            ],
            'Jus Fibar' => [
                'recommended' => ['Tahu Tepung', 'Ayam Rica Rica', 'Indomie Rebus'],
                'confidence' => 67.3,
                'support' => 0.42,
                'lift' => 1.3,
                'frequency_count' => 61
            ],
            'Jus Wortel' => [
                'recommended' => ['Kongkou Snack', 'Ayam Lombok Ijo', 'Kwetiau Goreng'],
                'confidence' => 64.7,
                'support' => 0.39,
                'lift' => 1.2,
                'frequency_count' => 52
            ],
            'Jeruk Panas' => [
                'recommended' => ['Ayam Rica Rica', 'Sambal Ijo', 'Indomie Goreng toping'],
                'confidence' => 79.8,
                'support' => 0.59,
                'lift' => 2.0,
                'frequency_count' => 101
            ],
            'Jeruk Dingin' => [
                'recommended' => ['Ayam Lada Hitam', 'Garang Asem Ayam', 'French Fries'],
                'confidence' => 77.5,
                'support' => 0.56,
                'lift' => 1.9,
                'frequency_count' => 95
            ],
            'Teh Manis Panas' => [
                'recommended' => ['Ayam Saos Padang', 'Roti Bakar', 'Mie Goreng'],
                'confidence' => 84.1,
                'support' => 0.66,
                'lift' => 2.3,
                'frequency_count' => 126
            ],
            'Teh Manis Dingin' => [
                'recommended' => ['Ayam Goreng', 'Nasi', 'Nugget'],
                'confidence' => 91.2,
                'support' => 0.76,
                'lift' => 2.9,
                'frequency_count' => 178
            ],
            'Coffe Ekspresso' => [
                'recommended' => ['Ayam Bakar', 'Sambal Pecak', 'Roti Bakar Keju Coklat'],
                'confidence' => 73.9,
                'support' => 0.51,
                'lift' => 1.6,
                'frequency_count' => 83
            ],
            'Cappucino Ice' => [
                'recommended' => ['Ayam Mentega', 'French Fries', 'Nasi Goreng Gemilang'],
                'confidence' => 72.6,
                'support' => 0.49,
                'lift' => 1.5,
                'frequency_count' => 76
            ],
            'Cappucino Hot' => [
                'recommended' => ['Sambal Bawang', 'Pisang Bakar', 'Kwetiau Rebus'],
                'confidence' => 69.8,
                'support' => 0.46,
                'lift' => 1.4,
                'frequency_count' => 67
            ],
            'Cofe Susu Gula Aren' => [
                'recommended' => ['Roti Bakar', 'Ayam Goreng', 'Indomie Rebus'],
                'confidence' => 75.4,
                'support' => 0.53,
                'lift' => 1.7,
                'frequency_count' => 89
            ],
            'Best Latte Ice' => [
                'recommended' => ['Keong Racun', 'Ayam Mentega', 'Nasi Goreng Seafood'],
                'confidence' => 68.7,
                'support' => 0.44,
                'lift' => 1.3,
                'frequency_count' => 64
            ],
            'Cofe Latte Ice' => [
                'recommended' => ['Tahu Tepung', 'Ayam Bakar', 'Mie Goreng'],
                'confidence' => 71.3,
                'support' => 0.48,
                'lift' => 1.5,
                'frequency_count' => 73
            ],
            'Cofe Latte Hot' => [
                'recommended' => ['Sosis Goreng', 'Sambal Terasi', 'Kwetiau Goreng'],
                'confidence' => 70.1,
                'support' => 0.47,
                'lift' => 1.4,
                'frequency_count' => 69
            ],
            'Matcha Ice' => [
                'recommended' => ['Nugget', 'Ayam Asam Manis', 'Nasi Goreng Ayam'],
                'confidence' => 66.5,
                'support' => 0.41,
                'lift' => 1.3,
                'frequency_count' => 57
            ],
            'Matcha Hot' => [
                'recommended' => ['Pisang Bakar', 'Ayam Lada Hitam', 'Indomie Goreng toping'],
                'confidence' => 65.2,
                'support' => 0.40,
                'lift' => 1.2,
                'frequency_count' => 53
            ],
            'Coklat Ice' => [
                'recommended' => ['Roti Bakar Keju Coklat', 'Ayam Rica Rica', 'Kwetiau Rebus'],
                'confidence' => 74.7,
                'support' => 0.52,
                'lift' => 1.6,
                'frequency_count' => 81
            ],
            'Coklat Hot' => [
                'recommended' => ['Kongkou Snack', 'Ayam Lombok Ijo', 'Nasi Goreng Gemilang'],
                'confidence' => 72.4,
                'support' => 0.49,
                'lift' => 1.5,
                'frequency_count' => 75
            ],
            'Red Valvet Ice' => [
                'recommended' => ['French Fries', 'Sambal Dabu Dabu', 'Mie Goreng'],
                'confidence' => 67.9,
                'support' => 0.43,
                'lift' => 1.3,
                'frequency_count' => 62
            ],
            'Red Valvet Hot' => [
                'recommended' => ['Keong Racun', 'Garang Asem Ayam', 'Indomie Rebus'],
                'confidence' => 66.1,
                'support' => 0.41,
                'lift' => 1.2,
                'frequency_count' => 56
            ],
            'Vakal Peach' => [
                'recommended' => ['Tahu Tepung', 'Ayam Saos Padang', 'Nasi Goreng Seafood'],
                'confidence' => 68.3,
                'support' => 0.44,
                'lift' => 1.3,
                'frequency_count' => 63
            ],
            'Beauty Peach' => [
                'recommended' => ['Sosis Goreng', 'Sambal Mentah', 'Kwetiau Goreng'],
                'confidence' => 69.6,
                'support' => 0.45,
                'lift' => 1.4,
                'frequency_count' => 66
            ],
            'Teh Tubruk' => [
                'recommended' => ['Ayam Lombok Ijo', 'Sambal Terasi', 'Roti Bakar'],
                'confidence' => 78.7,
                'support' => 0.58,
                'lift' => 1.9,
                'frequency_count' => 96
            ],
            'Teh Tubruk Susu' => [
                'recommended' => ['Sop Ayam', 'Nugget', 'Nasi Goreng Ayam'],
                'confidence' => 76.3,
                'support' => 0.55,
                'lift' => 1.8,
                'frequency_count' => 92
            ],

            // NASI DAN MIE -> rekomendasi ke kategori lain
            'Mie Goreng' => [
                'recommended' => ['Ayam Goreng', 'Teh Manis Panas', 'Nugget'],
                'confidence' => 85.8,
                'support' => 0.69,
                'lift' => 2.4,
                'frequency_count' => 142
            ],
            'Indomie Rebus' => [
                'recommended' => ['Sambal Terasi', 'Cofe Susu Gula Aren', 'Sosis Goreng'],
                'confidence' => 73.5,
                'support' => 0.51,
                'lift' => 1.6,
                'frequency_count' => 80
            ],
            'Indomie Goreng toping' => [
                'recommended' => ['Ayam Asam Manis', 'Jeruk Panas', 'French Fries'],
                'confidence' => 77.2,
                'support' => 0.56,
                'lift' => 1.8,
                'frequency_count' => 94
            ],
            'Nasi Goreng Gemilang' => [
                'recommended' => ['Sambal Bawang', 'Cappucino Ice', 'Keong Racun'],
                'confidence' => 81.6,
                'support' => 0.63,
                'lift' => 2.1,
                'frequency_count' => 109
            ],
            'Nasi Goreng Seafood' => [
                'recommended' => ['Jus Jeruk', 'Best Latte Ice', 'Tahu Tepung'],
                'confidence' => 74.9,
                'support' => 0.52,
                'lift' => 1.7,
                'frequency_count' => 84
            ],
            'Nasi Goreng Ayam' => [
                'recommended' => ['Jus Mangga', 'Teh Tubruk Susu', 'Pisang Bakar'],
                'confidence' => 79.1,
                'support' => 0.58,
                'lift' => 1.9,
                'frequency_count' => 99
            ],
            'Kwetiau Goreng' => [
                'recommended' => ['Sambal Ijo', 'Cofe Latte Hot', 'Kongkou Snack'],
                'confidence' => 71.8,
                'support' => 0.49,
                'lift' => 1.5,
                'frequency_count' => 77
            ],
            'Kwetiau Rebus' => [
                'recommended' => ['Jus Tomat', 'Cappucino Hot', 'Roti Bakar Keju Coklat'],
                'confidence' => 70.6,
                'support' => 0.47,
                'lift' => 1.4,
                'frequency_count' => 72
            ],

            // ANEKA SNACK -> rekomendasi ke kategori lain
            'French Fries' => [
                'recommended' => ['Ayam Goreng', 'Cappucino Ice', 'Indomie Goreng toping'],
                'confidence' => 83.4,
                'support' => 0.65,
                'lift' => 2.3,
                'frequency_count' => 127
            ],
            'Keong Racun' => [
                'recommended' => ['Sop Ayam', 'Best Latte Ice', 'Nasi Goreng Gemilang'],
                'confidence' => 74.2,
                'support' => 0.51,
                'lift' => 1.6,
                'frequency_count' => 81
            ],
            'Kongkou Snack' => [
                'recommended' => ['Ayam Lombok Ijo', 'Coklat Hot', 'Kwetiau Goreng'],
                'confidence' => 68.9,
                'support' => 0.44,
                'lift' => 1.3,
                'frequency_count' => 65
            ],
            'Nugget' => [
                'recommended' => ['Nasi', 'Teh Manis Dingin', 'Mie Goreng'],
                'confidence' => 87.3,
                'support' => 0.73,
                'lift' => 2.6,
                'frequency_count' => 149
            ],
            'Pisang Bakar' => [
                'recommended' => ['Cappucino Hot', 'Nasi Goreng Ayam', 'Jus Apel'],
                'confidence' => 72.7,
                'support' => 0.50,
                'lift' => 1.5,
                'frequency_count' => 78
            ],
            'Roti Bakar' => [
                'recommended' => ['Teh Manis Panas', 'Cofe Susu Gula Aren', 'Jus Alpukat'],
                'confidence' => 80.5,
                'support' => 0.61,
                'lift' => 2.0,
                'frequency_count' => 107
            ],
            'Roti Bakar Keju Coklat' => [
                'recommended' => ['Coffe Ekspresso', 'Coklat Ice', 'Kwetiau Rebus'],
                'confidence' => 75.8,
                'support' => 0.54,
                'lift' => 1.7,
                'frequency_count' => 86
            ],
            'Sosis Goreng' => [
                'recommended' => ['Jus Tomat', 'Cofe Latte Hot', 'Indomie Rebus'],
                'confidence' => 73.1,
                'support' => 0.50,
                'lift' => 1.6,
                'frequency_count' => 79
            ],
            'Tahu Tepung' => [
                'recommended' => ['Jus Fibar', 'Vakal Peach', 'Nasi Goreng Seafood'],
                'confidence' => 69.4,
                'support' => 0.45,
                'lift' => 1.4,
                'frequency_count' => 68
            ],
        ];

        $currentTime = Carbon::now();

        foreach ($rekomendasiData as $menuName => $data) {
            // Cari menu berdasarkan nama
            $menu = Menu::where('nama_menu', $menuName)->first();
            
            if ($menu) {
                // Cari ID menu yang direkomendasikan
                $recommendedIds = [];
                foreach ($data['recommended'] as $recommendedName) {
                    $recommendedMenu = Menu::where('nama_menu', $recommendedName)->first();
                    if ($recommendedMenu) {
                        $recommendedIds[] = $recommendedMenu->id;
                    }
                }
                
                if (!empty($recommendedIds)) {
                    RekomendasiMenu::create([
                        'menu_id' => $menu->id,
                        'recommended_menu_ids' => $recommendedIds,
                        'confidence' => $data['confidence'],
                        'support' => $data['support'],
                        'lift' => $data['lift'],
                        'frequency_count' => $data['frequency_count'],
                        'last_calculated_at' => $currentTime,
                        'created_at' => $currentTime,
                        'updated_at' => $currentTime
                    ]);
                } else {
                    $this->command->warn("Rekomendasi untuk '{$menuName}' tidak dapat dibuat karena menu yang direkomendasikan tidak ditemukan.");
                }
            } else {
                $this->command->warn("Menu '{$menuName}' tidak ditemukan dalam database.");
            }
        }

        $this->command->info('Enhanced Rekomendasi menu seeder completed successfully.');
        $this->command->info('Total recommendations created: ' . RekomendasiMenu::count());
        
        // Tampilkan statistik confidence
        $avgConfidence = RekomendasiMenu::avg('confidence');
        $maxConfidence = RekomendasiMenu::max('confidence');
        $minConfidence = RekomendasiMenu::min('confidence');
        
        $this->command->info('Confidence Statistics:');
        $this->command->info("- Average: " . round($avgConfidence, 2) . "%");
        $this->command->info("- Maximum: " . round($maxConfidence, 2) . "%");
        $this->command->info("- Minimum: " . round($minConfidence, 2) . "%");
        
        // Tampilkan statistik support
        $avgSupport = RekomendasiMenu::avg('support');
        $maxSupport = RekomendasiMenu::max('support');
        $minSupport = RekomendasiMenu::min('support');
        
        $this->command->info('Support Statistics:');
        $this->command->info("- Average: " . round($avgSupport, 3));
        $this->command->info("- Maximum: " . round($maxSupport, 3));
        $this->command->info("- Minimum: " . round($minSupport, 3));
        
        // Tampilkan statistik lift
        $avgLift = RekomendasiMenu::avg('lift');
        $maxLift = RekomendasiMenu::max('lift');
        $minLift = RekomendasiMenu::min('lift');
        
        $this->command->info('Lift Statistics:');
        $this->command->info("- Average: " . round($avgLift, 2));
        $this->command->info("- Maximum: " . round($maxLift, 2));
        $this->command->info("- Minimum: " . round($minLift, 2));
    }
}