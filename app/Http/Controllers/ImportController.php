<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DutyRate;
use App\Models\HsCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImportController extends Controller
{
    private const HEADERS = [
        'category_slug', 'hs_code', 'description',
        'rate_type', 'duty_pct', 'rate_per_kg', 'source_ref',
    ];
    private const MAX_ROWS = 1000;

    public function create()
    {
        return view('import.create');
    }

    public function template()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, self::HEADERS, ',', '"', '');
            fputcsv($out, ['phones-electronics', '8517.13', 'Smartphones', 'ad_valorem', '20', '', 'NCS Tariff, p.114'], ',', '"', '');
            fputcsv($out, ['machine-parts', '8413.70', 'Centrifugal pumps', 'per_kg', '', '150', 'NCS Tariff, p.200'], ',', '"', '');
            fclose($out);
        }, 'portwise-import-template.csv', ['Content-Type' => 'text/csv']);
    }

    public function store(Request $request)
    {
        dd(
            $request->file('file')?->getError(),
            $request->file('file')?->getErrorMessage(),
            ini_get('upload_max_filesize'),
            ini_get('upload_tmp_dir'),
            sys_get_temp_dir(),
            is_writable(sys_get_temp_dir()),
            php_ini_loaded_file(),
            PHP_SAPI
        );
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:2048']]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($handle, null, ',', '"', '');

        if (! $header) {
            return back()->withErrors(['file' => 'The file is empty.']);
        }

        // Normalise headers (Excel adds a BOM to "CSV UTF-8" files)
        $header = array_map(
            fn ($h) => strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $h))),
            $header
        );

        $missing = array_diff(self::HEADERS, $header);
        if ($missing) {
            return back()->withErrors(['file' => 'Missing columns: ' . implode(', ', $missing)]);
        }

        $categories = Category::pluck('id', 'slug'); // slug => id

        // ---- Pass 1: validate every row ----
        $valid = [];
        $errors = [];
        $seen = [];
        $line = 1;
        $count = 0;

        while (($raw = fgetcsv($handle, null, ',', '"', '')) !== false) {
            $line++;

            if (count(array_filter($raw, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue; // blank line
            }

            if (++$count > self::MAX_ROWS) {
                $errors[] = ['line' => $line, 'code' => '', 'messages' => ['Too many rows (max ' . self::MAX_ROWS . '). Split the file.']];
                break;
            }

            if (count($raw) !== count($header)) {
                $errors[] = ['line' => $line, 'code' => '', 'messages' => ['Wrong number of columns.']];
                continue;
            }

            $data = array_combine($header, array_map(fn ($v) => trim((string) $v), $raw));
            $data = array_map(fn ($v) => $v === '' ? null : $v, $data);

            $validator = Validator::make($data, [
                'category_slug' => ['required', Rule::in($categories->keys()->all())],
                'hs_code' => ['required', 'string', 'max:20', 'regex:/^[0-9.]+$/'],
                'description' => ['required', 'string', 'max:255'],
                'rate_type' => ['required', Rule::in(['ad_valorem', 'per_kg'])],
                'duty_pct' => ['nullable', 'required_if:rate_type,ad_valorem', 'numeric', 'between:0,100'],
                'rate_per_kg' => ['nullable', 'required_if:rate_type,per_kg', 'numeric', 'min:0'],
                'source_ref' => ['nullable', 'string', 'max:255'],
            ]);

            if ($validator->fails()) {
                $errors[] = ['line' => $line, 'code' => $data['hs_code'] ?? '', 'messages' => $validator->errors()->all()];
                continue;
            }

            if (isset($seen[$data['hs_code']])) {
                $errors[] = ['line' => $line, 'code' => $data['hs_code'], 'messages' => ["Duplicate of line {$seen[$data['hs_code']]} in this file."]];
                continue;
            }
            $seen[$data['hs_code']] = $line;

            // keep only the field that matches the rate type (DB CHECK constraint)
            if ($data['rate_type'] === 'ad_valorem') {
                $data['rate_per_kg'] = null;
            } else {
                $data['duty_pct'] = null;
            }

            $valid[] = $data;
        }
        fclose($handle);

        // ---- Pass 2: apply valid rows in ONE transaction ----
        $summary = ['codes_created' => 0, 'codes_updated' => 0, 'rates_created' => 0, 'rates_updated' => 0];

        try {
            DB::transaction(function () use ($valid, $categories, &$summary) {
                foreach ($valid as $r) {
                    $hs = HsCode::firstOrNew(['code' => $r['hs_code']]);
                    $hsIsNew = ! $hs->exists;

                    $hs->fill([
                        'category_id' => $categories[$r['category_slug']],
                        'description' => $r['description'],
                        'is_active' => true,
                    ])->save();
                    $summary[$hsIsNew ? 'codes_created' : 'codes_updated']++;

                    $rate = DutyRate::where('hs_code_id', $hs->id)->whereNull('effective_to')->first()
                        ?? new DutyRate(['hs_code_id' => $hs->id, 'effective_from' => now()->toDateString()]);
                    $rateIsNew = ! $rate->exists;

                    $rate->fill([
                        'rate_type' => $r['rate_type'],
                        'duty_pct' => $r['duty_pct'],
                        'rate_per_kg' => $r['rate_per_kg'],
                        'source_ref' => $r['source_ref'],
                    ])->save();
                    $summary[$rateIsNew ? 'rates_created' : 'rates_updated']++;
                }

                // TODO: write ONE audit_log row for this import here (audit log comes next)
            });
        } catch (\Throwable $e) {
            Log::error('CSV import failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['file' => 'The import failed and was rolled back. Nothing was saved. (' . $e->getMessage() . ')']);
        }

        return redirect()->route('import.create')->with('import_result', [
            'applied' => count($valid),
            'summary' => $summary,
            'errors' => $errors,
        ]);
    }
}