<?php

namespace App\Http\Controllers;

use App\Models\DutyRate;
use App\Http\Requests\DutyRateRequest;
use App\Models\Category;
use App\Models\HsCode;
use Illuminate\Http\Request;

class DutyRateController extends Controller
{
    public function index(Request $request)
    {
        $rates = DutyRate::query()
            ->select('duty_rates.*')
            ->join('hs_codes', 'hs_codes.id', '=', 'duty_rates.hs_code_id')
            ->with('hsCode.category')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('hs_codes.code', 'like', "{$s}%")
                ->orWhere('hs_codes.description', 'ilike', "%{$s}%")))
            ->when($request->category, fn ($q, $c) => $q->where('hs_codes.category_id', $c))
            ->when($request->status === 'current', fn ($q) => $q->whereNull('duty_rates.effective_to'))
            ->when($request->status === 'ended', fn ($q) => $q->whereNotNull('duty_rates.effective_to'))
            ->orderBy('hs_codes.code')
            ->orderByDesc('duty_rates.effective_from')
            ->paginate(15)
            ->withQueryString();

        return view('duty-rates.index', [
            'rates' => $rates,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        return view('duty-rates.create', ['hsCodes' => $this->hsCodeOptions()]);
    }

    public function store(DutyRateRequest $request)
    {
        DutyRate::create($request->validated());
        return redirect()->route('duty-rates.index')->with('status', 'Duty rate created.');
    }

    public function edit(DutyRate $dutyRate)
    {
        return view('duty-rates.edit', [
            'rate' => $dutyRate,
            'hsCodes' => $this->hsCodeOptions($dutyRate->hs_code_id),
        ]);
    }

    public function update(DutyRateRequest $request, DutyRate $dutyRate)
    {
        $dutyRate->update($request->validated());
        return redirect()->route('duty-rates.index')->with('status', 'Duty rate updated.');
    }

    public function destroy(DutyRate $dutyRate)
    {
        $dutyRate->delete();
        return redirect()->route('duty-rates.index')->with('status', 'Duty rate deleted.');
    }

    private function hsCodeOptions(?int $include = null)
    {
        return HsCode::where('is_active', true)
            ->when($include, fn ($q) => $q->orWhere('id', $include))
            ->orderBy('code')
            ->get(['id', 'code', 'description']);
    }
}
