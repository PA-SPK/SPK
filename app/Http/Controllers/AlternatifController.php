<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiAlternatif;
use App\Services\SPKCalculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AlternatifController extends Controller
{
    public function __construct(
        public SPKCalculationService $spkService
    ) {}

    /**
     * Store a newly created alternative.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:alternatifs,kode',
            'nama' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer',
        ]);

        $maxUrutan = Alternatif::max('urutan') ?? 0;
        $validated['urutan'] = $validated['urutan'] ?? ($maxUrutan + 1);
        $validated['kategori'] = $validated['kategori'] ?? 'Desa';

        $alternatif = Alternatif::create($validated);

        // Auto-assign default nilai 0 for all existing criteria
        $kriterias = Kriteria::all();
        $inputNilai = $request->input('nilai', []);

        foreach ($kriterias as $k) {
            $val = isset($inputNilai[$k->kode]) ? (float) $inputNilai[$k->kode] : 0.0;
            NilaiAlternatif::create([
                'alternatif_id' => $alternatif->id,
                'kriteria_id' => $k->id,
                'nilai' => $val,
            ]);
        }

        return redirect()->route('spk.alternatif')->with('success', "Alternatif {$alternatif->kode} ({$alternatif->nama}) berhasil ditambahkan.");
    }

    /**
     * Update the specified alternative details.
     */
    public function update(Request $request, Alternatif $alternatif): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:alternatifs,kode,' . $alternatif->id,
            'nama' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer',
        ]);

        $validated['kategori'] = $validated['kategori'] ?? 'Desa';
        $alternatif->update($validated);

        return redirect()->route('spk.alternatif')->with('success', "Alternatif {$alternatif->kode} ({$alternatif->nama}) berhasil diperbarui.");
    }

    /**
     * Remove the specified alternative.
     */
    public function destroy(string|int $alternatif): RedirectResponse
    {
        $item = Alternatif::find($alternatif);
        if (! $item) {
            $item = Alternatif::where('kode', $alternatif)->first();
        }

        if (! $item) {
            return redirect()->route('spk.alternatif')->with('warning', 'Alternatif tidak ditemukan atau sudah dihapus.');
        }

        $kode = $item->kode;
        $nama = $item->nama;
        $item->delete();

        return redirect()->route('spk.alternatif')->with('success', "Alternatif {$kode} ({$nama}) berhasil dihapus.");
    }

    /**
     * Bulk update decision matrix values (nilai_alternatifs).
     */
    public function updateNilai(Request $request): RedirectResponse
    {
        $matrix = $request->input('nilai', []);

        foreach ($matrix as $altId => $critValues) {
            $alternatif = Alternatif::find($altId);
            if (! $alternatif) {
                continue;
            }

            foreach ($critValues as $critId => $val) {
                $kriteria = Kriteria::find($critId);
                if (! $kriteria) {
                    continue;
                }

                NilaiAlternatif::updateOrCreate(
                    [
                        'alternatif_id' => $alternatif->id,
                        'kriteria_id' => $kriteria->id,
                    ],
                    [
                        'nilai' => (float) $val,
                    ]
                );
            }
        }

        return redirect()->route('spk.alternatif')->with('success', 'Matriks keputusan alternatif berhasil diperbarui dan perhitungan TOPSIS telah disesuaikan.');
    }

    /**
     * Export all alternatives and criteria values to a CSV file.
     */
    public function exportCsv(): StreamedResponse
    {
        $kriterias = Kriteria::orderBy('urutan')->get();
        $alternatifs = Alternatif::with('nilaiAlternatifs')->orderBy('urutan')->get();

        $filename = 'matriks_alternatif_spk_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->streamDownload(function () use ($kriterias, $alternatifs) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens it with proper encoding
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Header
            $header = ['Kode', 'Nama', 'Kategori', 'Deskripsi'];
            foreach ($kriterias as $k) {
                $header[] = $k->kode;
            }
            fputcsv($handle, $header);

            // CSV Data Rows
            foreach ($alternatifs as $alt) {
                $row = [
                    $alt->kode,
                    $alt->nama,
                    $alt->kategori ?? 'Umum',
                    $alt->deskripsi ?? '',
                ];

                $nilaiMap = $alt->nilaiAlternatifs->pluck('nilai', 'kriteria_id');

                foreach ($kriterias as $k) {
                    $row[] = (float) ($nilaiMap[$k->id] ?? 0.0);
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, $headers);
    }

    /**
     * Download a sample CSV template for importing alternatives.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $kriterias = Kriteria::orderBy('urutan')->get();
        $filename = 'template_import_alternatif_spk.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->streamDownload(function () use ($kriterias) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            $header = ['Kode', 'Nama', 'Kategori', 'Deskripsi'];
            foreach ($kriterias as $k) {
                $header[] = $k->kode;
            }
            fputcsv($handle, $header);

            // Sample rows
            $sampleRow1 = ['ALT01', 'Kandidat Alternatif 1', 'Desa', 'Contoh keterangan lokasi 1'];
            $sampleRow2 = ['ALT02', 'Kandidat Alternatif 2', 'Kelurahan', 'Contoh keterangan lokasi 2'];

            foreach ($kriterias as $index => $k) {
                $sampleRow1[] = 10 + ($index * 2);
                $sampleRow2[] = 15 + ($index * 3);
            }

            fputcsv($handle, $sampleRow1);
            fputcsv($handle, $sampleRow2);

            fclose($handle);
        }, $filename, $headers);
    }

    /**
     * Import alternatives and their criteria values from a CSV file.
     */
    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('file_csv');
        $path = $file->getRealPath();

        $content = file_get_contents($path);
        // Strip UTF-8 BOM if present
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        $lines = preg_split("/\r\n|\n|\r/", trim($content));
        if (empty($lines)) {
            return redirect()->route('spk.alternatif')->with('error', 'File CSV kosong atau tidak valid.');
        }

        // Determine delimiter (comma or semicolon)
        $firstLine = $lines[0];
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

        $header = str_getcsv(array_shift($lines), $delimiter);
        $header = array_map(fn($col) => trim($col, " \t\n\r\0\x0B\"'"), $header);

        $kodeIdx = array_search('Kode', $header, true);
        if ($kodeIdx === false) {
            $kodeIdx = array_search('kode', $header, true);
        }

        $namaIdx = array_search('Nama', $header, true);
        if ($namaIdx === false) {
            $namaIdx = array_search('nama', $header, true);
        }

        if ($kodeIdx === false || $namaIdx === false) {
            return redirect()->route('spk.alternatif')->with('error', 'File CSV harus memiliki kolom header "Kode" dan "Nama".');
        }

        $kategoriIdx = array_search('Kategori', $header, true);
        if ($kategoriIdx === false) {
            $kategoriIdx = array_search('kategori', $header, true);
        }

        $deskripsiIdx = array_search('Deskripsi', $header, true);
        if ($deskripsiIdx === false) {
            $deskripsiIdx = array_search('deskripsi', $header, true);
        }

        // Map criteria code to their column indices
        $allKriterias = Kriteria::all();
        $critIndices = [];
        foreach ($allKriterias as $k) {
            $idx = array_search($k->kode, $header, true);
            if ($idx === false) {
                $idx = array_search(strtolower($k->kode), array_map('strtolower', $header), true);
            }
            if ($idx !== false) {
                $critIndices[$k->id] = $idx;
            }
        }

        $importedCount = 0;
        $maxUrutan = Alternatif::max('urutan') ?? 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $row = str_getcsv($line, $delimiter);
            $kode = trim($row[$kodeIdx] ?? '');
            $nama = trim($row[$namaIdx] ?? '');

            if ($kode === '' || $nama === '') {
                continue;
            }

            $kategori = ($kategoriIdx !== false && isset($row[$kategoriIdx])) ? trim($row[$kategoriIdx]) : 'Umum';
            $deskripsi = ($deskripsiIdx !== false && isset($row[$deskripsiIdx])) ? trim($row[$deskripsiIdx]) : null;

            $alternatif = Alternatif::where('kode', $kode)->first();
            if (! $alternatif) {
                $maxUrutan++;
                $alternatif = Alternatif::create([
                    'kode' => $kode,
                    'nama' => $nama,
                    'kategori' => $kategori !== '' ? $kategori : 'Umum',
                    'deskripsi' => $deskripsi,
                    'urutan' => $maxUrutan,
                ]);
            } else {
                $alternatif->update([
                    'nama' => $nama,
                    'kategori' => $kategori !== '' ? $kategori : $alternatif->kategori,
                    'deskripsi' => $deskripsi ?? $alternatif->deskripsi,
                ]);
            }

            // Sync criteria scores
            foreach ($critIndices as $kriteriaId => $idx) {
                if (isset($row[$idx])) {
                    $valRaw = str_replace(',', '.', trim($row[$idx]));
                    $val = is_numeric($valRaw) ? (float) $valRaw : 0.0;

                    NilaiAlternatif::updateOrCreate(
                        [
                            'alternatif_id' => $alternatif->id,
                            'kriteria_id' => $kriteriaId,
                        ],
                        [
                            'nilai' => $val,
                        ]
                    );
                }
            }

            $importedCount++;
        }

        return redirect()->route('spk.alternatif')->with('success', "Berhasil mengimpor {$importedCount} data alternatif beserta nilai kriterianya dari file CSV.");
    }
}

