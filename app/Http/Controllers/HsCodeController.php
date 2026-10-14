<?php

namespace App\Http\Controllers;

use App\Models\HsCode;
use App\Http\Requests\StoreHsCodeRequest;
use App\Http\Requests\UpdateHsCodeRequest;
use Illuminate\Http\Request;
use App\Models\Category;

class HsCodeController extends Controller
{
    public function index(Request $request)
    {
        $hsCodes = HsCode::with('category')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('code', 'like', "{$s}%")
                ->orWhere('description', 'ilike', "%{$s}%")))
            ->when($request->category, fn ($q, $c) => $q->where('category_id', $c))
            ->orderBy('code')
            ->paginate(15)
            ->withQueryString();

        return view('hs-codes.index', [
            'hsCodes' => $hsCodes,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        return view('hs-codes.create', ['categories' => Category::orderBy('name')->get(['id', 'name'])]);
    }

    public function store(StoreHsCodeRequest $request)
    {
        HsCode::create($request->validated());
        return redirect()->route('hs-codes.index')->with('status', 'HS code created.');
    }

    public function edit(HsCode $hsCode)
    {
        return view('hs-codes.edit', [
            'hsCode' => $hsCode,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateHsCodeRequest $request, HsCode $hsCode)
    {
        $hsCode->update($request->validated());
        return redirect()->route('hs-codes.index')->with('status', 'HS code updated.');
    }

    public function destroy(HsCode $hsCode)
    {
        $hsCode->update(['is_active' => false]); // his doc: never hard-delete an HS code that has rates
        return redirect()->route('hs-codes.index')->with('status', 'HS code deactivated.');
    }
}
