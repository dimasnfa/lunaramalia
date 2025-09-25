<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DetailPesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Validate input first
        $this->validateDateInputs($request);

        $filterType = $request->input('filter_type', 'semua_data');
        $tanggal = $request->input('harian');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $bulan = $request->input('bulanan');
        $tahun = $request->input('tahunan');

        $query = DetailPesanan::with(['menu.kategori', 'pesanan']);

        // Apply filters only if not "semua_data"
        if ($filterType !== 'semua_data') {
            $query->whereHas('pesanan', function ($q) use ($filterType, $tanggal, $tanggalAwal, $tanggalAkhir, $bulan, $tahun) {
                if ($filterType === 'range_tanggal') {
                    if ($tanggalAwal && $tanggalAkhir) {
                        // Range tanggal
                        $q->whereDate('tanggal_pesanan', '>=', $tanggalAwal)
                          ->whereDate('tanggal_pesanan', '<=', $tanggalAkhir);
                    } elseif ($tanggalAwal) {
                        // Single date (tanggal awal saja)
                        $q->whereDate('tanggal_pesanan', $tanggalAwal);
                    }
                } elseif ($filterType === 'harian' && $tanggal) {
                    // Filter untuk tanggal tunggal
                    $q->whereDate('tanggal_pesanan', $tanggal);
                } elseif ($filterType === 'bulanan' && $bulan) {
                    $q->whereYear('tanggal_pesanan', substr($bulan, 0, 4))
                      ->whereMonth('tanggal_pesanan', substr($bulan, 5, 2));
                } elseif ($filterType === 'tahunan' && $tahun) {
                    $q->whereYear('tanggal_pesanan', $tahun);
                }
            });
        }

        // Fixed: Order by tanggal_pesanan ASC melalui relasi
        $laporans = $query->get()->sortBy(function($item) {
            return $item->pesanan->tanggal_pesanan . ' ' . ($item->pesanan->waktu_pesanan ?? '00:00:00');
        })->values();

        $totalPendapatan = $laporans->sum('subtotal');

        return view('admin.pages.laporan.index', compact(
            'laporans', 'filterType', 'tanggal', 'tanggalAwal', 'tanggalAkhir', 'bulan', 'tahun', 'totalPendapatan'
        ));
    }

    public function exportPdf(Request $request)
    {
        // Validate input first
        $this->validateDateInputs($request);

        // Force HTTPS for PDF export
        $request->server->set('HTTPS', 'on');
        $request->server->set('SERVER_PORT', 443);

        $filterType = $request->input('filter_type', 'semua_data');
        $tanggal = $request->input('harian');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $bulan = $request->input('bulanan');
        $tahun = $request->input('tahunan');

        $query = DetailPesanan::with(['menu.kategori', 'pesanan']);

        // Apply filters only if not "semua_data"
        if ($filterType !== 'semua_data') {
            $query->whereHas('pesanan', function ($q) use ($filterType, $tanggal, $tanggalAwal, $tanggalAkhir, $bulan, $tahun) {
                if ($filterType === 'range_tanggal') {
                    if ($tanggalAwal && $tanggalAkhir) {
                        $q->whereDate('tanggal_pesanan', '>=', $tanggalAwal)
                          ->whereDate('tanggal_pesanan', '<=', $tanggalAkhir);
                    } elseif ($tanggalAwal) {
                        $q->whereDate('tanggal_pesanan', $tanggalAwal);
                    }
                } elseif ($filterType === 'harian' && $tanggal) {
                    $q->whereDate('tanggal_pesanan', $tanggal);
                } elseif ($filterType === 'bulanan' && $bulan) {
                    $q->whereYear('tanggal_pesanan', substr($bulan, 0, 4))
                      ->whereMonth('tanggal_pesanan', substr($bulan, 5, 2));
                } elseif ($filterType === 'tahunan' && $tahun) {
                    $q->whereYear('tanggal_pesanan', $tahun);
                }
            });
        }

        // Fixed: Order by tanggal_pesanan ASC for PDF export
        $laporans = $query->get()->sortBy(function($item) {
            return $item->pesanan->tanggal_pesanan . ' ' . ($item->pesanan->waktu_pesanan ?? '00:00:00');
        })->values();

        $totalPendapatan = $laporans->sum('subtotal');

        // Enhanced PDF options for better security
        $pdf = Pdf::loadView('admin.pages.laporan.cetakpdf', [
            'laporans' => $laporans,
            'filterType' => $filterType,
            'tanggal' => $tanggal,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'tanggalCetak' => Carbon::now()->translatedFormat('d-m-Y'),
            'totalPendapatan' => $totalPendapatan,
            'filterDisplayText' => $this->getFilterDisplayText($filterType, $tanggal, $tanggalAwal, $tanggalAkhir, $bulan, $tahun)
        ])->setOptions([
            'isRemoteEnabled' => false, // Security: Disable remote content
            'isHtml5ParserEnabled' => true,
            'chroot' => public_path(), // Restrict file access
        ]);

        $filename = 'laporan-pesanan-' . now()->format('Y-m-d-His') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        // Validate input first
        $this->validateDateInputs($request);

        // Force HTTPS for Excel export
        $request->server->set('HTTPS', 'on');
        $request->server->set('SERVER_PORT', 443);

        $filterType = $request->input('filter_type', 'semua_data');
        $tanggal = $request->input('harian');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $bulan = $request->input('bulanan');
        $tahun = $request->input('tahunan');

        $query = DetailPesanan::with(['menu.kategori', 'pesanan.meja']);

        // Apply filters only if not "semua_data"
        if ($filterType !== 'semua_data') {
            $query->whereHas('pesanan', function ($q) use ($filterType, $tanggal, $tanggalAwal, $tanggalAkhir, $bulan, $tahun) {
                if ($filterType === 'range_tanggal') {
                    if ($tanggalAwal && $tanggalAkhir) {
                        $q->whereDate('tanggal_pesanan', '>=', $tanggalAwal)
                          ->whereDate('tanggal_pesanan', '<=', $tanggalAkhir);
                    } elseif ($tanggalAwal) {
                        $q->whereDate('tanggal_pesanan', $tanggalAwal);
                    }
                } elseif ($filterType === 'harian' && $tanggal) {
                    $q->whereDate('tanggal_pesanan', $tanggal);
                } elseif ($filterType === 'bulanan' && $bulan) {
                    $q->whereYear('tanggal_pesanan', substr($bulan, 0, 4))
                      ->whereMonth('tanggal_pesanan', substr($bulan, 5, 2));
                } elseif ($filterType === 'tahunan' && $tahun) {
                    $q->whereYear('tanggal_pesanan', $tahun);
                }
            });
        }

        // Fixed: Order by tanggal_pesanan ASC for Excel export
        $laporans = $query->get()->sortBy(function($item) {
            return $item->pesanan->tanggal_pesanan . ' ' . ($item->pesanan->waktu_pesanan ?? '00:00:00');
        })->values();

        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set sheet title
        $sheet->setTitle('Laporan Transaksi');

        // Header informasi
        $sheet->setCellValue('A1', 'LAPORAN TRANSAKSI PESANAN');
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Filter information
        $filterText = $this->getFilterDisplayText($filterType, $tanggal, $tanggalAwal, $tanggalAkhir, $bulan, $tahun);
        $sheet->setCellValue('A2', 'Filter: ' . $filterText);
        $sheet->mergeCells('A2:O2');
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Tanggal cetak
        $sheet->setCellValue('A3', 'Dicetak pada: ' . Carbon::now()->translatedFormat('d F Y H:i:s'));
        $sheet->mergeCells('A3:O3');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header kolom (starting from row 5)
        $headers = [
            'A5' => 'No',
            'B5' => 'Tanggal',
            'C5' => 'Waktu',
            'D5' => 'Jenis Pesanan',
            'E5' => 'Menu',
            'F5' => 'Kategori',
            'G5' => 'Jumlah',
            'H5' => 'Harga Satuan',
            'I5' => 'Subtotal',
            'J5' => 'Total Harga Pesanan',
            'K5' => 'Nomor Meja',
            'L5' => 'Tipe Meja',
            'M5' => 'Lantai',
            'N5' => 'Nama Pelanggan',
            'O5' => 'Nomor WhatsApp'
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '007BFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];

        $sheet->getStyle('A5:O5')->applyFromArray($headerStyle);

        // Fill data
        $row = 6;
        $no = 1;
        $totalPendapatan = 0;

        foreach ($laporans as $laporan) {
            $pesanan = $laporan->pesanan;
            $meja = $pesanan->meja ?? null;
            
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, \Carbon\Carbon::parse($pesanan->tanggal_pesanan)->format('d-m-Y'));
            $sheet->setCellValue('C' . $row, $pesanan->waktu_pesanan ?? '-');
            $sheet->setCellValue('D' . $row, ucfirst($pesanan->jenis_pesanan ?? ($meja ? 'dine in' : 'takeaway')));
            $sheet->setCellValue('E' . $row, $laporan->menu->nama_menu ?? '-');
            $sheet->setCellValue('F' . $row, $laporan->menu->kategori->nama_kategori ?? '-');
            $sheet->setCellValue('G' . $row, $laporan->jumlah);
            // PERBAIKAN: Gunakan harga dari menu, bukan dari detail_pesanan
            $sheet->setCellValue('H' . $row, $laporan->menu->harga ?? 0);
            $sheet->setCellValue('I' . $row, $laporan->subtotal);
            $sheet->setCellValue('J' . $row, $pesanan->total_harga);
            $sheet->setCellValue('K' . $row, $meja->nomor_meja ?? '-');
            $sheet->setCellValue('L' . $row, $meja->tipe_meja ?? '-');
            $sheet->setCellValue('M' . $row, $meja->lantai ?? '-');
            $sheet->setCellValue('N' . $row, $pesanan->nama_pelanggan ?? '-');
            $sheet->setCellValue('O' . $row, $pesanan->nomor_wa ?? '-');

            // Format currency columns
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('#,##0');

            $totalPendapatan += $laporan->subtotal;
            $row++;
        }

        // Add total row
        if ($laporans->count() > 0) {
            $totalRow = $row + 1;
            $sheet->setCellValue('A' . $totalRow, 'TOTAL PENDAPATAN');
            $sheet->mergeCells('A' . $totalRow . ':H' . $totalRow);
            $sheet->setCellValue('I' . $totalRow, $totalPendapatan);
            
            $totalStyle = [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFFF00']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ];
            
            $sheet->getStyle('A' . $totalRow . ':I' . $totalRow)->applyFromArray($totalStyle);
            $sheet->getStyle('I' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
        }

        // Style data rows
        if ($laporans->count() > 0) {
            $dataRange = 'A6:O' . ($row - 1);
            $dataStyle = [
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ];
            $sheet->getStyle($dataRange)->applyFromArray($dataStyle);
        }

        // Auto-size columns
        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(5)->setRowHeight(20);

        // Generate filename
        $filename = 'laporan-pesanan-' . now()->format('Ymd_His') . '.xlsx';

        // Create writer and output
        $writer = new Xlsx($spreadsheet);
        
        // Set headers for download
        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Helper method to validate date inputs
     */
    private function validateDateInputs(Request $request)
    {
        $rules = [
            'filter_type' => 'in:semua_data,range_tanggal,harian,bulanan,tahunan',
            'harian' => 'nullable|date',
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'bulanan' => 'nullable|date_format:Y-m',
            'tahunan' => 'nullable|integer|min:2000|max:' . date('Y'),
        ];

        $messages = [
            'filter_type.in' => 'Tipe filter tidak valid.',
            'harian.date' => 'Format tanggal harian tidak valid.',
            'tanggal_awal.date' => 'Format tanggal awal tidak valid.',
            'tanggal_akhir.date' => 'Format tanggal akhir tidak valid.',
            'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal awal.',
            'bulanan.date_format' => 'Format bulan tidak valid (gunakan format YYYY-MM).',
            'tahunan.integer' => 'Tahun harus berupa angka.',
            'tahunan.min' => 'Tahun minimal 2000.',
            'tahunan.max' => 'Tahun maksimal ' . date('Y') . '.',
        ];

        try {
            $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors gracefully
            return redirect()->route('admin.laporan.index')
                           ->withErrors($e->validator)
                           ->withInput();
        }
    }

    /**
     * Get filter display text for UI
     */
    private function getFilterDisplayText($filterType, $tanggal, $tanggalAwal, $tanggalAkhir, $bulan, $tahun)
    {
        switch ($filterType) {
            case 'harian':
                return $tanggal ? 'Harian: ' . \Carbon\Carbon::parse($tanggal)->format('d/m/Y') : '';
            case 'range_tanggal':
                $text = 'Periode: ';
                if ($tanggalAwal) {
                    $text .= \Carbon\Carbon::parse($tanggalAwal)->format('d/m/Y');
                    if ($tanggalAkhir && $tanggalAkhir !== $tanggalAwal) {
                        $text .= ' - ' . \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y');
                    }
                }
                return $text;
            case 'bulanan':
                return $bulan ? 'Bulanan: ' . \Carbon\Carbon::parse($bulan.'-01')->format('F Y') : '';
            case 'tahunan':
                return $tahun ? 'Tahunan: ' . $tahun : '';
            default:
                return 'Semua Data';
        }
    }

    /**
     * Get summary statistics for the filtered data
     */
    private function getSummaryStatistics($laporans)
    {
        $totalTransaksi = $laporans->groupBy('pesanan.id')->count();
        $totalItem = $laporans->sum('jumlah');
        $totalPendapatan = $laporans->sum('subtotal');
        $rataRataPerTransaksi = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

        return [
            'total_transaksi' => $totalTransaksi,
            'total_item' => $totalItem,
            'total_pendapatan' => $totalPendapatan,
            'rata_rata_per_transaksi' => $rataRataPerTransaksi
        ];
    }
}