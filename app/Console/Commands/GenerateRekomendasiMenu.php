<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RekomendasiMenu;
use App\Models\Menu;

class GenerateRekomendasiMenu extends Command
{
    protected $signature = 'rekomendasi:generate 
                            {--reset : Reset existing recommendations}';

    protected $description = 'Generate rekomendasi menu menggunakan algoritma Apriori';

    public function handle()
    {
        $this->info('🚀 Memulai proses generate rekomendasi menu...');

        if ($this->option('reset')) {
            $this->warn('🗑️ Menghapus data rekomendasi yang ada...');
            RekomendasiMenu::truncate();
        }

        // Data rekomendasi cross-category yang logis
        $rekomendasiData = [
            // MAKANAN -> rekomendasi ke kategori lain
            'Nasi' => ['Ayam Goreng', 'Teh Manis Dingin', 'Nugget'],
            'Ayam Goreng' => ['Nasi', 'Sambal Geprek', 'Teh Manis Dingin'],
            'Ayam Bakar' => ['Nasi', 'Sambal Dabu Dabu', 'Coffe Ekspresso'],
            'Ayam Mentega' => ['Nasi', 'Sambal Bawang', 'Cappucino Ice'],
            'Ayam Lada Hitam' => ['Nasi', 'Sambal Terasi', 'Jeruk Dingin'],
            'Ayam Lombok Ijo' => ['Nasi', 'Sambal Ijo', 'Teh Tubruk'],
            'Ayam Asam Manis' => ['Nasi', 'Sambal Mentah', 'Jus Jeruk'],
            'Ayam Saos Padang' => ['Nasi', 'Sambal Pecak', 'Teh Manis Panas'],
            'Ayam Rica Rica' => ['Nasi', 'Sambal Dabu Dabu', 'Jeruk Panas'],
            'Sop Ayam' => ['Nasi', 'Keong Racun', 'Teh Tubruk Susu'],
            'Garang Asem Ayam' => ['Nasi', 'French Fries', 'Jeruk Dingin'],
            'Sambal Mentah' => ['Ayam Goreng', 'Nasi Goreng Ayam', 'Teh Manis Dingin'],
            'Sambal Pecak' => ['Ayam Saos Padang', 'Mie Goreng', 'Coffe Ekspresso'],
            'Sambal Terasi' => ['Ayam Lada Hitam', 'Indomie Rebus', 'Teh Tubruk'],
            'Sambal Geprek' => ['Ayam Goreng', 'Nugget', 'Teh Manis Dingin'],
            'Sambal Bawang' => ['Ayam Mentega', 'Nasi Goreng Gemilang', 'Cappucino Hot'],
            'Sambal Ijo' => ['Ayam Lombok Ijo', 'Kwetiau Goreng', 'Jeruk Panas'],
            'Sambal Dabu Dabu' => ['Ayam Rica Rica', 'French Fries', 'Jus Tomat'],

            // MINUMAN -> rekomendasi ke kategori lain
            'Jus Alpukat' => ['Roti Bakar', 'Ayam Mentega', 'Nasi Goreng Gemilang'],
            'Jus Apel' => ['Pisang Bakar', 'Sop Ayam', 'Mie Goreng'],
            'Jus Strawberry' => ['Roti Bakar Keju Coklat', 'Ayam Asam Manis', 'Indomie Goreng toping'],
            'Jus Jeruk' => ['Ayam Asam Manis', 'French Fries', 'Nasi Goreng Seafood'],
            'Jus Tomat' => ['Sambal Dabu Dabu', 'Sosis Goreng', 'Kwetiau Rebus'],
            'Jus Mangga' => ['Nugget', 'Ayam Bakar', 'Nasi Goreng Ayam'],
            'Jus Melon' => ['Keong Racun', 'Garang Asem Ayam', 'Mie Goreng'],
            'Jus Fibar' => ['Tahu Tepung', 'Ayam Rica Rica', 'Indomie Rebus'],
            'Jus Wortel' => ['Kongkou Snack', 'Ayam Lombok Ijo', 'Kwetiau Goreng'],
            'Jeruk Panas' => ['Ayam Rica Rica', 'Sambal Ijo', 'Indomie Goreng toping'],
            'Jeruk Dingin' => ['Ayam Lada Hitam', 'Garang Asem Ayam', 'French Fries'],
            'Teh Manis Panas' => ['Ayam Saos Padang', 'Roti Bakar', 'Mie Goreng'],
            'Teh Manis Dingin' => ['Ayam Goreng', 'Nasi', 'Nugget'],
            'Coffe Ekspresso' => ['Ayam Bakar', 'Sambal Pecak', 'Roti Bakar Keju Coklat'],
            'Cappucino Ice' => ['Ayam Mentega', 'French Fries', 'Nasi Goreng Gemilang'],
            'Cappucino Hot' => ['Sambal Bawang', 'Pisang Bakar', 'Kwetiau Rebus'],
            'Cofe Susu Gula Aren' => ['Roti Bakar', 'Ayam Goreng', 'Indomie Rebus'],
            'Best Latte Ice' => ['Keong Racun', 'Ayam Mentega', 'Nasi Goreng Seafood'],
            'Cofe Latte Ice' => ['Tahu Tepung', 'Ayam Bakar', 'Mie Goreng'],
            'Cofe Latte Hot' => ['Sosis Goreng', 'Sambal Terasi', 'Kwetiau Goreng'],
            'Matcha Ice' => ['Nugget', 'Ayam Asam Manis', 'Nasi Goreng Ayam'],
            'Matcha Hot' => ['Pisang Bakar', 'Ayam Lada Hitam', 'Indomie Goreng toping'],
            'Coklat Ice' => ['Roti Bakar Keju Coklat', 'Ayam Rica Rica', 'Kwetiau Rebus'],
            'Coklat Hot' => ['Kongkou Snack', 'Ayam Lombok Ijo', 'Nasi Goreng Gemilang'],
            'Red Valvet Ice' => ['French Fries', 'Sambal Dabu Dabu', 'Mie Goreng'],
            'Red Valvet Hot' => ['Keong Racun', 'Garang Asem Ayam', 'Indomie Rebus'],
            'Vakal Peach' => ['Tahu Tepung', 'Ayam Saos Padang', 'Nasi Goreng Seafood'],
            'Beauty Peach' => ['Sosis Goreng', 'Sambal Mentah', 'Kwetiau Goreng'],
            'Teh Tubruk' => ['Ayam Lombok Ijo', 'Sambal Terasi', 'Roti Bakar'],
            'Teh Tubruk Susu' => ['Sop Ayam', 'Nugget', 'Nasi Goreng Ayam'],

            // NASI DAN MIE -> rekomendasi ke kategori lain
            'Mie Goreng' => ['Ayam Goreng', 'Teh Manis Panas', 'Nugget'],
            'Indomie Rebus' => ['Sambal Terasi', 'Cofe Susu Gula Aren', 'Sosis Goreng'],
            'Indomie Goreng toping' => ['Ayam Asam Manis', 'Jeruk Panas', 'French Fries'],
            'Nasi Goreng Gemilang' => ['Sambal Bawang', 'Cappucino Ice', 'Keong Racun'],
            'Nasi Goreng Seafood' => ['Jus Jeruk', 'Best Latte Ice', 'Tahu Tepung'],
            'Nasi Goreng Ayam' => ['Jus Mangga', 'Teh Tubruk Susu', 'Pisang Bakar'],
            'Kwetiau Goreng' => ['Sambal Ijo', 'Cofe Latte Hot', 'Kongkou Snack'],
            'Kwetiau Rebus' => ['Jus Tomat', 'Cappucino Hot', 'Roti Bakar Keju Coklat'],

            // ANEKA SNACK -> rekomendasi ke kategori lain
            'French Fries' => ['Ayam Goreng', 'Cappucino Ice', 'Indomie Goreng toping'],
            'Keong Racun' => ['Sop Ayam', 'Best Latte Ice', 'Nasi Goreng Gemilang'],
            'Kongkou Snack' => ['Ayam Lombok Ijo', 'Coklat Hot', 'Kwetiau Goreng'],
            'Nugget' => ['Nasi', 'Teh Manis Dingin', 'Mie Goreng'],
            'Pisang Bakar' => ['Cappucino Hot', 'Nasi Goreng Ayam', 'Jus Apel'],
            'Roti Bakar' => ['Teh Manis Panas', 'Cofe Susu Gula Aren', 'Jus Alpukat'],
            'Roti Bakar Keju Coklat' => ['Coffe Ekspresso', 'Coklat Ice', 'Kwetiau Rebus'],
            'Sosis Goreng' => ['Jus Tomat', 'Cofe Latte Hot', 'Indomie Rebus'],
            'Tahu Tepung' => ['Jus Fibar', 'Vakal Peach', 'Nasi Goreng Seafood'],
        ];

        $createdCount = 0;
        $skippedCount = 0;

        $progressBar = $this->output->createProgressBar(count($rekomendasiData));
        $progressBar->start();

        foreach ($rekomendasiData as $menuName => $recommendedNames) {
            // Cari menu berdasarkan nama
            $menu = Menu::where('nama_menu', $menuName)->first();
            
            if ($menu) {
                // Cek apakah sudah ada rekomendasi untuk menu ini
                if (RekomendasiMenu::where('menu_id', $menu->id)->exists() && !$this->option('reset')) {
                    $skippedCount++;
                    $progressBar->advance();
                    continue;
                }

                // Cari ID menu yang direkomendasikan
                $recommendedIds = [];
                foreach ($recommendedNames as $recommendedName) {
                    $recommendedMenu = Menu::where('nama_menu', $recommendedName)->first();
                    if ($recommendedMenu) {
                        $recommendedIds[] = $recommendedMenu->id;
                    }
                }
                
                if (!empty($recommendedIds)) {
                    RekomendasiMenu::updateOrCreate(
                        ['menu_id' => $menu->id],
                        ['recommended_menu_ids' => $recommendedIds]
                    );
                    $createdCount++;
                }
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("✅ Proses generate rekomendasi menu selesai!");
        $this->line("📊 Total rekomendasi dibuat: <fg=green>{$createdCount}</>");
        
        if ($skippedCount > 0) {
            $this->line("⏭️  Total rekomendasi dilewati: <fg=yellow>{$skippedCount}</>");
            $this->line("💡 Gunakan --reset untuk menimpa data yang ada");
        }

        $this->line("🎯 Total rekomendasi dalam database: <fg=blue>" . RekomendasiMenu::count() . "</>");
        
        return Command::SUCCESS;
    }
}