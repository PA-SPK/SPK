<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\KriteriaPerbandingan;
use App\Models\NilaiAlternatif;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportController extends Controller
{
    /**
     * Import SPK data from an Excel or CSV file.
     *
     * Supported formats:
     * - Single-sheet study case workbook: contains AHP pairwise comparison table and TOPSIS decision matrix on one sheet.
     * - Multi-sheet Excel (.xlsx/.xls): Two sheets — "Kriteria" and "Alternatif".
     * - Clean CSV (.csv): Header "Kode,Nama,Kategori,Deskripsi,[K1],[K2],...".
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file_spk' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
            'mode'     => 'nullable|in:replace,merge',
        ]);

        $file      = $request->file('file_spk');
        $mode      = $request->input('mode', 'replace');
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            if (in_array($extension, ['xlsx', 'xls'], true)) {
                if (!class_exists(\ZipArchive::class) && $extension === 'xlsx') {
                    throw new \Exception('Ekstensi PHP ZipArchive belum aktif di web server. Silakan Restart Laragon (klik "Stop" lalu "Start All" di Laragon), atau simpan file Anda sebagai format CSV (.csv) lalu upload kembali.');
                }
                return $this->importExcel($file->getRealPath(), $mode);
            }

            return $this->importCsv($file->getRealPath(), $mode);
        } catch (\Throwable $e) {
            return redirect()->route('spk.import')
                ->with('error', 'Gagal mengimpor file: ' . $e->getMessage());
        }
    }

    /**
     * Download an Excel template with Kriteria and Alternatif sheets pre-formatted.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        // ── Sheet 1: Kriteria ────────────────────────────────────────────────
        $sheetKriteria = $spreadsheet->getActiveSheet();
        $sheetKriteria->setTitle('Kriteria');

        $headerStyleK = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '006948']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $dataStyleK = [
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        $kHeaders = ['Kode', 'Nama Parameter', 'Satuan', 'Tipe (Benefit/Cost)', 'Urutan'];
        $sheetKriteria->fromArray([$kHeaders], null, 'A1');
        $sheetKriteria->getStyle('A1:E1')->applyFromArray($headerStyleK);

        $sampleKriterias = [
            ['K1', 'Kepadatan Penduduk',   'Jiwa/km²',    'Benefit', 1],
            ['K2', 'Jarak ke Fasilitas',   'Km',          'Cost',    2],
            ['K3', 'Kasus Penyakit',       'Kasus/Tahun', 'Benefit', 3],
            ['K4', 'Tingkat Sanitasi',     'Skor (1-5)',  'Benefit', 4],
            ['K5', 'Jumlah Penduduk',      'Jiwa',        'Benefit', 5],
            ['K6', 'Status Jalan',         'Skor (1-5)',  'Benefit', 6],
        ];
        $sheetKriteria->fromArray($sampleKriterias, null, 'A2');
        $sheetKriteria->getStyle('A2:E7')->applyFromArray($dataStyleK);

        foreach (['A' => 10, 'B' => 35, 'C' => 18, 'D' => 22, 'E' => 10] as $col => $width) {
            $sheetKriteria->getColumnDimension($col)->setWidth($width);
        }

        // Add a note row for Tipe
        $sheetKriteria->setCellValue('A9', '* Benefit = semakin tinggi semakin baik | Cost = semakin rendah semakin baik');
        $sheetKriteria->getStyle('A9')->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF888888'));
        $sheetKriteria->mergeCells('A9:E9');

        // ── Sheet 2: Alternatif ───────────────────────────────────────────────
        $sheetAlt = $spreadsheet->createSheet();
        $sheetAlt->setTitle('Alternatif');

        $headerStyleA = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '006948']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $dataStyleA = [
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];

        // Build header dynamically from K1-K6 (matches sample kriteria)
        $aHeaders = ['Kode', 'Nama', 'Kategori', 'Deskripsi', 'K1', 'K2', 'K3', 'K4', 'K5', 'K6'];
        $sheetAlt->fromArray([$aHeaders], null, 'A1');
        $sheetAlt->getStyle('A1:J1')->applyFromArray($headerStyleA);

        $sampleAlts = [
            ['L1', 'Desa Mekar Jaya',       'Desa',      'Contoh keterangan 1', 350, 15, 45, 3, 2500, 3],
            ['L2', 'Desa Karya Bakti',       'Desa',      'Contoh keterangan 2', 400, 10, 60, 4, 1800, 4],
            ['L3', 'Kelurahan Mekar Sari',   'Kelurahan', 'Contoh keterangan 3', 280, 20, 35, 2, 3200, 2],
        ];
        $sheetAlt->fromArray($sampleAlts, null, 'A2');
        $sheetAlt->getStyle('A2:J4')->applyFromArray($dataStyleA);

        foreach (['A' => 8, 'B' => 30, 'C' => 14, 'D' => 30, 'E' => 8, 'F' => 8, 'G' => 8, 'H' => 8, 'I' => 8, 'J' => 8] as $col => $width) {
            $sheetAlt->getColumnDimension($col)->setWidth($width);
        }

        $sheetAlt->setCellValue('A6', '* Kolom K1-K6 harus sesuai dengan Kode kriteria di sheet "Kriteria". Tambah/kurangi kolom kriteria sesuai kebutuhan.');
        $sheetAlt->getStyle('A6')->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF888888'));
        $sheetAlt->mergeCells('A6:J6');

        // Set active sheet to first
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'template_import_spk.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Import from an Excel file (supports single-sheet study case or 2-sheet template).
     */
    private function importExcel(string $path, string $mode): RedirectResponse
    {
        $spreadsheet = IOFactory::load($path);

        // Check if the first sheet is a single-sheet study case workbook
        $firstSheet = $spreadsheet->getSheet(0);
        $firstSheetRows = $firstSheet->toArray(null, true, true, false);

        if ($this->isStudyCaseFormat($firstSheetRows)) {
            return $this->importStudyCaseFormat($firstSheetRows, $mode);
        }

        // Otherwise process as multi-sheet template
        $sheetKriteriaObj = null;
        $sheetAltObj      = null;

        foreach ($spreadsheet->getSheetNames() as $name) {
            $lower = mb_strtolower(trim($name));
            if (str_contains($lower, 'kriteri')) {
                $sheetKriteriaObj = $spreadsheet->getSheetByName($name);
            } elseif (str_contains($lower, 'alternatif') || str_contains($lower, 'matriks') || str_contains($lower, 'lokasi')) {
                $sheetAltObj = $spreadsheet->getSheetByName($name);
            }
        }

        // Fallback: 2 sheets → index 0=Kriteria, 1=Alternatif; 1 sheet → Alternatif only
        if ($sheetKriteriaObj === null && $spreadsheet->getSheetCount() >= 2) {
            $sheetKriteriaObj = $spreadsheet->getSheet(0);
            $sheetAltObj      = $sheetAltObj ?? $spreadsheet->getSheet(1);
        } elseif ($sheetKriteriaObj === null && $spreadsheet->getSheetCount() === 1) {
            $sheetAltObj = $sheetAltObj ?? $spreadsheet->getSheet(0);
        }

        if ($mode === 'replace') {
            $this->clearAllData();
        }

        $importedKrit = 0;
        $importedAlt  = 0;

        // ── Import Kriteria sheet ─────────────────────────────────────────────
        if ($sheetKriteriaObj !== null) {
            $rows      = $sheetKriteriaObj->toArray(null, true, true, false);
            $headerRow = array_shift($rows);
            $headerMap = $this->buildHeaderMap($headerRow ?? []);

            foreach ($rows as $row) {
                $kode = trim((string) ($row[$headerMap['kode'] ?? -1] ?? ''));
                $nama = trim((string) ($row[$headerMap['nama'] ?? $headerMap['nama parameter'] ?? -1] ?? ''));

                if ($kode === '' || $nama === '') {
                    continue;
                }

                $tipe = trim((string) ($row[$headerMap['tipe'] ?? $headerMap['tipe (benefit/cost)'] ?? -1] ?? 'Benefit'));
                $tipeClean = str_contains(strtolower($tipe), 'cost') ? 'Cost' : 'Benefit';

                $satuan = trim((string) ($row[$headerMap['satuan'] ?? -1] ?? ''));
                $urutan = (int) ($row[$headerMap['urutan'] ?? -1] ?? ($importedKrit + 1));

                Kriteria::updateOrCreate(
                    ['kode' => strtoupper($kode)],
                    [
                        'nama'   => $nama,
                        'satuan' => $satuan ?: null,
                        'tipe'   => $tipeClean,
                        'bobot'  => 0.0,
                        'urutan' => $urutan,
                    ]
                );

                $importedKrit++;
            }
        }

        // ── Import Alternatif sheet ───────────────────────────────────────────
        if ($sheetAltObj !== null) {
            $rows      = $sheetAltObj->toArray(null, true, true, false);
            $headerRow = array_shift($rows);
            $headerMap = $this->buildHeaderMap($headerRow ?? []);

            $allKriterias = Kriteria::orderBy('urutan')->get()->keyBy(fn($k) => strtoupper($k->kode));
            $critColMap   = [];

            foreach ($headerMap as $label => $colIdx) {
                $upper = strtoupper($label);
                if ($allKriterias->has($upper)) {
                    $critColMap[$allKriterias[$upper]->id] = $colIdx;
                }
            }

            $maxUrutan = Alternatif::max('urutan') ?? 0;

            foreach ($rows as $row) {
                $kode = trim((string) ($row[$headerMap['kode'] ?? -1] ?? ''));
                $nama = trim((string) ($row[$headerMap['nama'] ?? -1] ?? ''));

                if ($kode === '' || $nama === '') {
                    continue;
                }

                $kategori  = trim((string) ($row[$headerMap['kategori'] ?? -1] ?? 'Umum'));
                $deskripsi = trim((string) ($row[$headerMap['deskripsi'] ?? $headerMap['keterangan'] ?? $headerMap['catatan'] ?? -1] ?? ''));

                $maxUrutan++;
                $alternatif = Alternatif::updateOrCreate(
                    ['kode' => strtoupper($kode)],
                    [
                        'nama'      => $nama,
                        'kategori'  => $kategori ?: 'Umum',
                        'deskripsi' => $deskripsi ?: null,
                        'urutan'    => $maxUrutan,
                    ]
                );

                foreach ($critColMap as $kriteriaId => $colIdx) {
                    $rawVal = $row[$colIdx] ?? null;
                    $val    = is_numeric($rawVal) ? (float) $rawVal : 0.0;

                    NilaiAlternatif::updateOrCreate(
                        ['alternatif_id' => $alternatif->id, 'kriteria_id' => $kriteriaId],
                        ['nilai' => $val]
                    );
                }

                foreach ($allKriterias as $k) {
                    if (! isset($critColMap[$k->id])) {
                        NilaiAlternatif::firstOrCreate(
                            ['alternatif_id' => $alternatif->id, 'kriteria_id' => $k->id],
                            ['nilai' => 0.0]
                        );
                    }
                }

                $importedAlt++;
            }
        }

        // Reset AHP pairwise matrix to equal importance (1:1) if not already set
        $this->resetPairwiseMatrix();

        $summary = "Import selesai: {$importedKrit} kriteria dan {$importedAlt} alternatif berhasil dimuat.";
        return redirect()->route('spk.dashboard')->with('success', $summary);
    }

    /**
     * Import from a CSV file.
     */
    private function importCsv(string $path, string $mode): RedirectResponse
    {
        $content = file_get_contents($path);
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        $lines = preg_split("/\r\n|\n|\r/", trim($content));
        if (empty($lines)) {
            return redirect()->route('spk.alternatif')->with('error', 'File CSV kosong atau tidak valid.');
        }

        $firstLine = $lines[0];
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

        // Parse lines into 2D array
        $rows = array_map(fn($line) => str_getcsv($line, $delimiter), $lines);

        // If the CSV is a single-sheet study case report
        if ($this->isStudyCaseFormat($rows)) {
            return $this->importStudyCaseFormat($rows, $mode);
        }

        // Standard CSV: Alternatif import
        $headerRow = array_shift($rows);
        $headerMap = $this->buildHeaderMap($headerRow);

        if ($mode === 'replace') {
            NilaiAlternatif::query()->delete();
            Alternatif::query()->delete();
        }

        $allKriterias = Kriteria::orderBy('urutan')->get()->keyBy(fn($k) => strtoupper($k->kode));
        $critColMap   = [];

        foreach ($headerMap as $label => $colIdx) {
            $upper = strtoupper($label);
            if ($allKriterias->has($upper)) {
                $critColMap[$allKriterias[$upper]->id] = $colIdx;
            }
        }

        $importedAlt = 0;
        $maxUrutan   = Alternatif::max('urutan') ?? 0;

        foreach ($rows as $row) {
            $kode = trim($row[$headerMap['kode'] ?? -1] ?? '');
            $nama = trim($row[$headerMap['nama'] ?? -1] ?? '');

            if ($kode === '' || $nama === '') {
                continue;
            }

            $kategori  = trim($row[$headerMap['kategori'] ?? -1] ?? 'Umum');
            $deskripsi = trim($row[$headerMap['deskripsi'] ?? $headerMap['keterangan'] ?? -1] ?? '');

            $maxUrutan++;
            $alternatif = Alternatif::updateOrCreate(
                ['kode' => strtoupper($kode)],
                [
                    'nama'      => $nama,
                    'kategori'  => $kategori ?: 'Umum',
                    'deskripsi' => $deskripsi ?: null,
                    'urutan'    => $maxUrutan,
                ]
            );

            foreach ($critColMap as $kriteriaId => $colIdx) {
                $rawVal = $row[$colIdx] ?? null;
                $val    = is_numeric($rawVal) ? (float) $rawVal : 0.0;

                NilaiAlternatif::updateOrCreate(
                    ['alternatif_id' => $alternatif->id, 'kriteria_id' => $kriteriaId],
                    ['nilai' => $val]
                );
            }

            foreach ($allKriterias as $k) {
                if (! isset($critColMap[$k->id])) {
                    NilaiAlternatif::firstOrCreate(
                        ['alternatif_id' => $alternatif->id, 'kriteria_id' => $k->id],
                        ['nilai' => 0.0]
                    );
                }
            }

            $importedAlt++;
        }

        return redirect()->route('spk.alternatif')
            ->with('success', "Import CSV selesai: {$importedAlt} alternatif berhasil dimuat.");
    }

    /**
     * Check if a 2D sheet row array corresponds to a single-sheet study-case format.
     *
     * @param array<int, array<int, mixed>> $rows
     */
    private function isStudyCaseFormat(array $rows): bool
    {
        $hasAHP = false;
        $hasTOPSIS = false;

        foreach ($rows as $row) {
            $lineStr = strtolower(implode(' ', array_filter($row, fn($c) => $c !== null && $c !== '')));
            if (str_contains($lineStr, 'matriks perbandingan') || str_contains($lineStr, 'metode ahp') || str_contains($lineStr, 'tahap 1')) {
                $hasAHP = true;
            }
            if (str_contains($lineStr, 'matriks keputusan') || str_contains($lineStr, 'topsis') || str_contains($lineStr, 'lokasi usulan')) {
                $hasTOPSIS = true;
            }
        }

        return $hasAHP || $hasTOPSIS;
    }

    /**
     * Smart parser for single-sheet study-case workbooks.
     *
     * Extracts Criteria, AHP Pairwise Comparisons, and Alternatives with Decision Matrix.
     *
     * @param array<int, array<int, mixed>> $rows
     */
    private function importStudyCaseFormat(array $rows, string $mode): RedirectResponse
    {
        if ($mode === 'replace') {
            $this->clearAllData();
        }

        $totalRows   = count($rows);
        $kriterias   = [];
        $pairwise    = []; // [k1_code][k2_code] => float
        $alternatifs = [];

        // 1. Scan for Criteria definitions: e.g. "K1 Kepadatan Penduduk (Jiwa/km2)", "Benefit"
        for ($r = 0; $r < $totalRows; $r++) {
            $row = $rows[$r];
            foreach ($row as $colIdx => $cell) {
                $val = trim((string) $cell);
                if (preg_match('/^(K\d+)\s+([^(]+?)(?:\s*\(([^)]+)\))?$/iu', $val, $m)) {
                    $code   = strtoupper(trim($m[1]));
                    $name   = trim($m[2]);
                    $satuan = isset($m[3]) ? trim($m[3]) : null;

                    // Search adjacent cells in row for Benefit / Cost
                    $type = 'Benefit';
                    for ($next = $colIdx + 1; $next <= min($colIdx + 3, count($row) - 1); $next++) {
                        $adj = strtolower(trim((string) ($row[$next] ?? '')));
                        if ($adj === 'cost') {
                            $type = 'Cost';
                            break;
                        }
                        if ($adj === 'benefit') {
                            $type = 'Benefit';
                            break;
                        }
                    }

                    if (! isset($kriterias[$code])) {
                        $kriterias[$code] = [
                            'kode'   => $code,
                            'nama'   => $name,
                            'satuan' => $satuan,
                            'tipe'   => $type,
                            'urutan' => (int) filter_var($code, FILTER_SANITIZE_NUMBER_INT),
                        ];
                    }
                }
            }
        }

        // 2. Scan for Pairwise Comparison Matrix (AHP)
        for ($r = 0; $r < $totalRows; $r++) {
            $row       = $rows[$r];
            $firstCell = strtolower(trim((string) ($row[0] ?? '')));
            if (str_contains($firstCell, 'kriteria')) {
                $critCols = [];
                for ($c = 1; $c < count($row); $c++) {
                    $cVal = strtoupper(trim((string) ($row[$c] ?? '')));
                    if (preg_match('/^K\d+$/', $cVal)) {
                        $critCols[$c] = $cVal;
                    }
                }

                if (count($critCols) >= 2) {
                    for ($r2 = $r + 1; $r2 < min($r + 1 + count($critCols), $totalRows); $r2++) {
                        $rowK     = $rows[$r2];
                        $rowLabel = trim((string) ($rowK[0] ?? ''));
                        if (preg_match('/^(K\d+)/i', $rowLabel, $km)) {
                            $k1 = strtoupper($km[1]);
                            foreach ($critCols as $cIdx => $k2) {
                                $cellVal = $rowK[$cIdx] ?? null;
                                if (is_numeric($cellVal) && (float) $cellVal > 0) {
                                    $pairwise[$k1][$k2] = (float) $cellVal;
                                }
                            }
                        }
                    }
                    break;
                }
            }
        }

        // 3. Scan for Decision Matrix (Alternatif)
        for ($r = 0; $r < $totalRows; $r++) {
            $row = $rows[$r];
            $c0  = strtolower(trim((string) ($row[0] ?? '')));
            $c1  = strtolower(trim((string) ($row[1] ?? '')));

            if ($c0 === 'kode' && (str_contains($c1, 'lokasi') || str_contains($c1, 'alternatif') || str_contains($c1, 'nama'))) {
                $altCritCols = [];
                for ($c = 2; $c < count($row); $c++) {
                    $cVal = strtoupper(trim((string) ($row[$c] ?? '')));
                    if (preg_match('/^K\d+$/', $cVal)) {
                        $altCritCols[$c] = $cVal;
                    }
                }

                for ($r2 = $r + 1; $r2 < $totalRows; $r2++) {
                    $altRow  = $rows[$r2];
                    $altKode = trim((string) ($altRow[0] ?? ''));
                    $altNama = trim((string) ($altRow[1] ?? ''));

                    if ($altKode === '' || str_contains(strtolower($altNama), 'pembagi') || str_contains(strtolower($altKode), 'matriks')) {
                        break;
                    }

                    $scores = [];
                    foreach ($altCritCols as $cIdx => $kCode) {
                        $val = $altRow[$cIdx] ?? 0;
                        $scores[$kCode] = is_numeric($val) ? (float) $val : 0.0;
                    }

                    $alternatifs[] = [
                        'kode'   => $altKode,
                        'nama'   => $altNama,
                        'scores' => $scores,
                    ];
                }
                break;
            }
        }

        // ── Save Criteria to Database ─────────────────────────────────────────
        $savedKriterias = [];
        foreach ($kriterias as $kData) {
            $model = Kriteria::updateOrCreate(
                ['kode' => $kData['kode']],
                [
                    'nama'   => $kData['nama'],
                    'satuan' => $kData['satuan'],
                    'tipe'   => $kData['tipe'],
                    'bobot'  => 0.0,
                    'urutan' => $kData['urutan'],
                ]
            );
            $savedKriterias[$kData['kode']] = $model;
        }

        // ── Save Pairwise Comparisons (if present) ────────────────────────────
        if (! empty($pairwise)) {
            KriteriaPerbandingan::query()->delete();
            foreach ($pairwise as $k1Code => $targets) {
                if (! isset($savedKriterias[$k1Code])) {
                    continue;
                }
                $k1Id = $savedKriterias[$k1Code]->id;

                foreach ($targets as $k2Code => $val) {
                    if (! isset($savedKriterias[$k2Code])) {
                        continue;
                    }
                    $k2Id = $savedKriterias[$k2Code]->id;

                    KriteriaPerbandingan::updateOrCreate(
                        ['kriteria_1_id' => $k1Id, 'kriteria_2_id' => $k2Id],
                        ['nilai' => $val]
                    );
                }
            }
        } else {
            $this->resetPairwiseMatrix();
        }

        // ── Save Alternatives & Decision Matrix ───────────────────────────────
        $maxUrutan = Alternatif::max('urutan') ?? 0;
        foreach ($alternatifs as $altData) {
            $maxUrutan++;
            $alt = Alternatif::updateOrCreate(
                ['kode' => strtoupper($altData['kode'])],
                [
                    'nama'      => $altData['nama'],
                    'kategori'  => 'Umum',
                    'deskripsi' => 'Data dari import studi kasus',
                    'urutan'    => $maxUrutan,
                ]
            );

            foreach ($altData['scores'] as $kCode => $val) {
                if (isset($savedKriterias[$kCode])) {
                    NilaiAlternatif::updateOrCreate(
                        ['alternatif_id' => $alt->id, 'kriteria_id' => $savedKriterias[$kCode]->id],
                        ['nilai' => $val]
                    );
                }
            }
        }

        $kCount   = count($kriterias);
        $aCount   = count($alternatifs);
        $hasPairwise = ! empty($pairwise);

        $msg = "Import berhasil! Terdeteksi {$kCount} kriteria dan {$aCount} alternatif dari lembar studi kasus.";
        if ($hasPairwise) {
            $msg .= " Nilai matriks perbandingan AHP juga telah dimuat otomatis.";
        }

        return redirect()->route('spk.dashboard')->with('success', $msg);
    }

    /**
     * Delete all SPK data (criteria, alternatives, pairwise comparisons, scores).
     */
    private function clearAllData(): void
    {
        NilaiAlternatif::query()->delete();
        KriteriaPerbandingan::query()->delete();
        Alternatif::query()->delete();
        Kriteria::query()->delete();
    }

    /**
     * Reset pairwise comparison matrix to equal importance (all values = 1).
     */
    private function resetPairwiseMatrix(): void
    {
        KriteriaPerbandingan::query()->delete();

        $kriterias = Kriteria::all();
        foreach ($kriterias as $k1) {
            foreach ($kriterias as $k2) {
                KriteriaPerbandingan::updateOrCreate(
                    ['kriteria_1_id' => $k1->id, 'kriteria_2_id' => $k2->id],
                    ['nilai' => 1.0]
                );
            }
        }
    }

    /**
     * Build a normalised header → column-index map (0-based index).
     *
     * @param  array<mixed> $headerRow
     * @return array<string, int>
     */
    private function buildHeaderMap(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $idx => $cell) {
            $label       = mb_strtolower(trim((string) $cell));
            $map[$label] = $idx;
        }

        return $map;
    }
}
