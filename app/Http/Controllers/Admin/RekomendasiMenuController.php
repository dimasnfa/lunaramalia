<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Kategori;
use App\Models\RekomendasiMenu;
use App\Models\DetailPesanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RekomendasiMenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('kategori')->get();
        return view('rekomendasi.index', compact('menus'));
    }

    public function getRecommendationsForMenu(Request $request)
    {
        $selectedMenuIds = $request->input('selected_menus', []);
        $currentMenuId = $request->input('current_menu_id');

        Log::info('Rekomendasi request received', [
            'selected_menus' => $selectedMenuIds,
            'current_menu_id' => $currentMenuId
        ]);

        if (empty($selectedMenuIds)) {
            return response()->json([
                'status' => true,
                'recommendations' => $this->getFallbackRecommendations([]),
                'algorithm_used' => 'default',
            ]);
        }

        $recommendations = [];
        $algorithmUsed = 'default';

        // 1. Coba ambil rekomendasi dari Apriori
        $aprioriRecommendations = $this->getAprioriRecommendations($selectedMenuIds, $currentMenuId);
        
        if (!empty($aprioriRecommendations)) {
            $recommendations = $aprioriRecommendations;
            $algorithmUsed = 'apriori';
            Log::info('Using Apriori recommendations', ['count' => count($recommendations)]);
        } else {
            // 2. Gunakan metode fallback yang lebih cerdas
            $recommendations = $this->getIntelligentFallbackRecommendations($selectedMenuIds, $currentMenuId);
            $algorithmUsed = 'intelligent_fallback';
            Log::info('Using intelligent fallback recommendations', ['count' => count($recommendations)]);
        }

        return response()->json([
            'status' => true,
            'recommendations' => $recommendations,
            'algorithm_used' => $algorithmUsed,
        ]);
    }

    private function getAprioriRecommendations(array $selectedMenuIds, $currentMenuId = null): array
    {
        $allRecommendedIds = [];
        
        // Ambil rekomendasi untuk setiap menu yang dipilih
        $aprioriRules = RekomendasiMenu::whereIn('menu_id', $selectedMenuIds)->get();
        
        foreach ($aprioriRules as $rule) {
            if (is_array($rule->recommended_menu_ids)) {
                $allRecommendedIds = array_merge($allRecommendedIds, $rule->recommended_menu_ids);
            }
        }
        
        if (empty($allRecommendedIds)) {
            return [];
        }
        
        // Hilangkan duplikasi dan menu yang sudah dipilih
        $allRecommendedIds = array_unique($allRecommendedIds);
        $allRecommendedIds = array_diff($allRecommendedIds, $selectedMenuIds);
        
        // Ambil detail menu yang direkomendasikan
        $recommendedMenus = Menu::with('kategori')
            ->whereIn('id', $allRecommendedIds)
            ->where('stok', '>', 0)
            ->get();
        
        $recommendations = [];
        foreach ($recommendedMenus as $menu) {
            $recommendations[] = $this->formatMenuData($menu, 'Rekomendasi berdasarkan pola pembelian pelanggan');
        }
        
        // Ambil maksimal 3 rekomendasi
        return array_slice($recommendations, 0, 3);
    }

    private function getIntelligentFallbackRecommendations(array $selectedMenuIds, $currentMenuId = null): array
    {
        $recommendations = [];
        
        // Ambil kategori dari menu yang dipilih
        $selectedCategories = Menu::whereIn('id', $selectedMenuIds)->pluck('kategori_id')->unique()->toArray();
        
        // 1. Rekomendasi berdasarkan popularitas dari kategori berbeda
        $popularFromOtherCategories = DetailPesanan::select('menu_id', DB::raw('COUNT(*) as order_count'))
            ->with('menu.kategori')
            ->whereHas('menu', function($query) use ($selectedCategories, $selectedMenuIds) {
                $query->whereNotIn('kategori_id', $selectedCategories)
                      ->whereNotIn('id', $selectedMenuIds)
                      ->where('stok', '>', 0);
            })
            ->groupBy('menu_id')
            ->orderByDesc('order_count')
            ->limit(2)
            ->get();

        foreach ($popularFromOtherCategories as $item) {
            if ($item->menu) {
                $recommendations[] = $this->formatMenuData(
                    $item->menu, 
                    'Menu populer dari kategori berbeda',
                    85 + rand(0, 10) // Confidence score 85-95%
                );
            }
        }
        
        // 2. Jika masih kurang, ambil dari kategori yang sama tapi belum dipilih
        if (count($recommendations) < 3) {
            $remainingSlots = 3 - count($recommendations);
            
            $sameCategory = DetailPesanan::select('menu_id', DB::raw('COUNT(*) as order_count'))
                ->with('menu.kategori')
                ->whereHas('menu', function($query) use ($selectedCategories, $selectedMenuIds) {
                    $query->whereIn('kategori_id', $selectedCategories)
                          ->whereNotIn('id', $selectedMenuIds)
                          ->where('stok', '>', 0);
                })
                ->groupBy('menu_id')
                ->orderByDesc('order_count')
                ->limit($remainingSlots)
                ->get();

            foreach ($sameCategory as $item) {
                if ($item->menu) {
                    $recommendations[] = $this->formatMenuData(
                        $item->menu, 
                        'Menu populer dari kategori serupa',
                        70 + rand(0, 15) // Confidence score 70-85%
                    );
                }
            }
        }
        
        // 3. Jika masih kurang, ambil menu random yang tersedia
        if (count($recommendations) < 3) {
            $remainingSlots = 3 - count($recommendations);
            $existingIds = array_merge($selectedMenuIds, array_column($recommendations, 'id'));
            
            $randomMenus = Menu::with('kategori')
                ->whereNotIn('id', $existingIds)
                ->where('stok', '>', 0)
                ->inRandomOrder()
                ->limit($remainingSlots)
                ->get();

            foreach ($randomMenus as $menu) {
                $recommendations[] = $this->formatMenuData(
                    $menu, 
                    'Pilihan spesial untuk Anda',
                    50 + rand(0, 20) // Confidence score 50-70%
                );
            }
        }
        
        return $recommendations;
    }

    private function getFallbackRecommendations(array $selectedMenuIds): array
    {
        // Menu paling populer secara keseluruhan
        $popularMenus = DetailPesanan::select('menu_id', DB::raw('COUNT(*) as total_pesanan'))
            ->with('menu.kategori')
            ->whereHas('menu', function($query) use ($selectedMenuIds) {
                $query->whereNotIn('id', $selectedMenuIds)->where('stok', '>', 0);
            })
            ->groupBy('menu_id')
            ->orderByDesc('total_pesanan')
            ->limit(3)
            ->get();

        $recommendations = [];
        foreach ($popularMenus as $item) {
            if ($item->menu) {
                $recommendations[] = $this->formatMenuData(
                    $item->menu, 
                    'Menu terpopuler di Gemilang Cafe',
                    60 + rand(0, 20)
                );
            }
        }
        
        // Jika tidak ada data popularitas, ambil random
        if (empty($recommendations)) {
            $randomMenus = Menu::with('kategori')
                ->whereNotIn('id', $selectedMenuIds)
                ->where('stok', '>', 0)
                ->inRandomOrder()
                ->limit(3)
                ->get();

            foreach ($randomMenus as $menu) {
                $recommendations[] = $this->formatMenuData($menu, 'Rekomendasi untuk Anda');
            }
        }

        return $recommendations;
    }

    private function formatMenuData($menu, $ruleText = '', $confidence = null): array
    {
        // Mapping gambar custom
        $customImages = $this->getCustomImageMapping();
        $folder = $this->getFolderFromCategory($menu->kategori->nama_kategori ?? '');
        
        $gambar = 'default.png';
        if (isset($customImages[$menu->kategori->nama_kategori][$menu->nama_menu])) {
            $gambar = $customImages[$menu->kategori->nama_kategori][$menu->nama_menu];
        } elseif ($menu->gambar) {
            $gambar = $menu->gambar;
        }

        return [
            'id' => $menu->id,
            'nama_menu' => $menu->nama_menu,
            'harga' => $menu->harga,
            'gambar' => $gambar,
            'kategori' => $menu->kategori->nama_kategori ?? '',
            'rule_text' => $ruleText,
            'confidence' => $confidence ?? rand(60, 90)
        ];
    }

    private function getFolderFromCategory($kategori): string
    {
        $folderMap = [
            'Makanan' => 'makanan',
            'Minuman' => 'minuman', 
            'Nasi dan Mie' => 'nasi-dan-mie',
            'Aneka Snack' => 'aneka-snack'
        ];
        return $folderMap[$kategori] ?? 'default';
    }

    private function getCustomImageMapping(): array
    {
        return [
            'Makanan' => [
                'Nasi' => 'nasi.png',
                'Ayam Goreng' => 'ayamgoreng.jpg',
                'Ayam Bakar' => 'ayambakar.jpg',
                'Ayam Mentega' => 'ayamentega.jpg',
                'Ayam Lada Hitam' => 'ayamladahitam.jpg',
                'Ayam Lombok Ijo' => 'ayamlombokijo.jpg',
                'Ayam Asam Manis' => 'ayamasammanis.jpg',
                'Ayam Saos Padang' => 'ayamsaospadang.jpg',
                'Ayam Rica Rica' => 'ayamricarica.jpg',
                'Sop Ayam' => 'sopayam.jpg',
                'Garang Asem Ayam' => 'garangasem.jpg',
                'Sambal Mentah' => 'sambalmentah.jpg',
                'Sambal Pecak' => 'sambalpecak.jpg',
                'Sambal Terasi' => 'sambalterasi.jpg',
                'Sambal Geprek' => 'sambalgeprek.jpg',
                'Sambal Bawang' => 'sambalbawang.jpg',
                'Sambal Ijo' => 'sambalijo.jpg',
                'Sambal Dabu Dabu' => 'sambaldabu.jpg',
            ],
            'Minuman' => [
                'Jus Alpukat' => 'alpukat.jpg',
                'Jus Apel' => 'apel.jpg',
                'Jus Strawberry' => 'strawberry.jpg',
                'Jus Jeruk' => 'jeruk.jpg',
                'Jus Tomat' => 'tomat.jpg',
                'Jus Mangga' => 'mangga.jpg',
                'Jus Melon' => 'melon.jpg',
                'Jus Fibar' => 'fibar.jpg',
                'Jus Wortel' => 'wortel.jpg',
                'Jeruk Panas' => 'jerukpanas.jpg',
                'Jeruk Dingin' => 'jerukdingin.jpg',
                'Teh Manis Panas' => 'tehmanis.jpg',
                'Teh Manis Dingin' => 'tehmanis.jpg',
                'Coffe Ekspresso' => 'coffekspresso.jpg',
                'Cappucino Ice' => 'cappucinoice.jpg',
                'Cappucino Hot' => 'cappucinohot.jpg',
                'Cofe Susu Gula Aren' => 'coffesusugularen.jpg',
                'Best Latte Ice' => 'bestlattehot.jpg',
                'Cofe Latte Ice' => 'coffelatteice.jpg',
                'Cofe Latte Hot' => 'coffelatehot.jpg',
                'Matcha Ice' => 'macthaice.jpg',
                'Matcha Hot' => 'matchahot.jpg',
                'Coklat Ice' => 'coklatice.jpg',
                'Coklat Hot' => 'coklathot.jpg',
                'Red Valvet Ice' => 'redvlvt.jpg',
                'Red Valvet Hot' => 'redvlvt.jpg',
                'Vakal Peach' => 'vakalpeach.jpg',
                'Beauty Peach' => 'beautypeach.jpg',
                'Teh Tubruk' => 'tehtubruk.jpg',
                'Teh Tubruk Susu' => 'tehtubruk2.jpg',
            ],
            'Nasi dan Mie' => [
                'Mie Goreng' => 'miegoreng.png',
                'Indomie Rebus' => 'indomierebus.png',
                'Indomie Goreng toping' => 'indomiegoreng.png',
                'Nasi Goreng Gemilang' => 'nasigorenggemilang.png',
                'Nasi Goreng Seafood' => 'nasigorengseafood.png',
                'Nasi Goreng Ayam' => 'nasigorengayam.png',
                'Kwetiau Goreng' => 'kwetiau.png',
                'Kwetiau Rebus' => 'kwetiaurebus.png',
            ],
            'Aneka Snack' => [
                'French Fries' => 'frenchfries.jpg',
                'Keong Racun' => 'keongracun.jpg',
                'Kongkou Snack' => 'KongkouSnack.jpg',
                'Nugget' => 'nugget.jpg',
                'Pisang Bakar' => 'pisangbakar.jpg',
                'Roti Bakar' => 'rotibakar.png',
                'Roti Bakar Keju Coklat' => 'rotibakarkejucoklat.jpg',
                'Sosis Goreng' => 'sosisgoreng.jpg',
                'Tahu Tepung' => 'tahutepung.jpg',
            ],
        ];
    }

    // Method untuk generate data rekomendasi Apriori (untuk seeding)
    public function generateAprioriData()
    {
        // Contoh data rekomendasi yang logis berdasarkan kategori
        $rekomendasiData = [
            // Makanan -> rekomendasi ke kategori lain
            1 => [2, 15, 25], // Nasi -> Ayam Goreng, Teh Manis Dingin, Nugget
            2 => [3, 16, 26], // Ayam Goreng -> Ayam Bakar, Coffe Ekspresso, French Fries
            3 => [1, 17, 27], // Ayam Bakar -> Nasi, Cappucino Ice, Keong Racun
            4 => [2, 18, 28], // Ayam Mentega -> Ayam Goreng, Cappucino Hot, Kongkou Snack
            5 => [1, 19, 29], // Ayam Lada Hitam -> Nasi, Cofe Susu Gula Aren, Nugget
            
            // Minuman -> rekomendasi ke kategori lain
            15 => [1, 2, 25], // Teh Manis Dingin -> Nasi, Ayam Goreng, Nugget
            16 => [3, 4, 26], // Coffe Ekspresso -> Ayam Bakar, Ayam Mentega, French Fries
            17 => [2, 5, 27], // Cappucino Ice -> Ayam Goreng, Ayam Lada Hitam, Keong Racun
            18 => [1, 3, 28], // Cappucino Hot -> Nasi, Ayam Bakar, Kongkou Snack
            19 => [4, 5, 29], // Cofe Susu Gula Aren -> Ayam Mentega, Ayam Lada Hitam, Nugget
            
            // Nasi dan Mie -> rekomendasi ke kategori lain
            20 => [2, 16, 25], // Mie Goreng -> Ayam Goreng, Coffe Ekspresso, Nugget
            21 => [3, 17, 26], // Indomie Rebus -> Ayam Bakar, Cappucino Ice, French Fries
            22 => [1, 15, 27], // Indomie Goreng toping -> Nasi, Teh Manis Dingin, Keong Racun
            23 => [4, 18, 28], // Nasi Goreng Gemilang -> Ayam Mentega, Cappucino Hot, Kongkou Snack
            24 => [2, 19, 29], // Nasi Goreng Seafood -> Ayam Goreng, Cofe Susu Gula Aren, Nugget
            
            // Aneka Snack -> rekomendasi ke kategori lain
            25 => [1, 15, 20], // Nugget -> Nasi, Teh Manis Dingin, Mie Goreng
            26 => [2, 16, 21], // French Fries -> Ayam Goreng, Coffe Ekspresso, Indomie Rebus
            27 => [3, 17, 22], // Keong Racun -> Ayam Bakar, Cappucino Ice, Indomie Goreng toping
            28 => [4, 18, 23], // Kongkou Snack -> Ayam Mentega, Cappucino Hot, Nasi Goreng Gemilang
            29 => [5, 19, 24], // Pisang Bakar -> Ayam Lada Hitam, Cofe Susu Gula Aren, Nasi Goreng Seafood
        ];

        // Hapus data lama
        RekomendasiMenu::truncate();

        // Insert data baru
        foreach ($rekomendasiData as $menuId => $recommendedIds) {
            RekomendasiMenu::create([
                'menu_id' => $menuId,
                'recommended_menu_ids' => $recommendedIds
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data rekomendasi Apriori berhasil digenerate'
        ]);
    }
}