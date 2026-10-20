<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VariantType;
use App\Models\VariantValue;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VariantTypeController extends Controller
{
    public function index()
    {
        $variantTypes = VariantType::withCount('values')
            ->latest()
            ->paginate(15);

        return view('admin.pages.variant-types.index', compact('variantTypes'));
    }

    public function create()
    {
        return view('admin.pages.variant-types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'input_type' => ['required', Rule::in(['text', 'color'])],
            'status' => ['required', 'boolean'],
        ]);

        VariantType::create($data);

        return redirect()
            ->route('admin.variant-types.index')
            ->with('success', __('dashboard.variant_types.created'));
    }

    public function edit(VariantType $variantType)
    {
        $variantType->load([
            'values' => fn ($query) => $query->orderBy('id'),
        ]);

        return view('admin.pages.variant-types.edit', compact('variantType'));
    }

    public function update(Request $request, VariantType $variantType)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'input_type' => ['required', Rule::in(['text', 'color'])],
            'status' => ['required', 'boolean'],
        ]);

        $variantType->update($data);

        if ($data['input_type'] !== 'color') {
            $variantType->values()->update(['color_code' => null]);
        }

        return redirect()
            ->route('admin.variant-types.edit', $variantType)
            ->with('success', __('dashboard.variant_types.updated'));
    }

    public function destroy(VariantType $variantType)
    {
        $variantType->delete();

        return redirect()
            ->route('admin.variant-types.index')
            ->with('success', __('dashboard.variant_types.deleted'));
    }

    public function storeValue(Request $request, VariantType $variantType)
    {
        $rules = [
            'value_ar' => ['required', 'string', 'max:255'],
            'value_en' => [
                'required',
                'string',
                'max:255',
                Rule::unique('variant_values', 'value_en')
                    ->where('variant_type_id', $variantType->id),
            ],
            'status' => ['required', 'boolean'],
        ];

        $rules['color_code'] = $variantType->input_type === 'color'
            ? ['required', 'regex:/^#[0-9A-Fa-f]{6}$/']
            : ['nullable', 'prohibited'];

        $data = $request->validate($rules);

        if ($variantType->input_type !== 'color') {
            $data['color_code'] = null;
        }

        $variantType->values()->create($data);

        return redirect()
            ->route('admin.variant-types.edit', $variantType)
            ->with('success', __('dashboard.variant_types.value_created'));
    }

    public function updateValue(
        Request $request,
        VariantType $variantType,
        VariantValue $variantValue
    ) {
        abort_unless(
            $variantValue->variant_type_id === $variantType->id,
            404
        );

        $rules = [
            'value_ar' => ['required', 'string', 'max:255'],
            'value_en' => [
                'required',
                'string',
                'max:255',
                Rule::unique('variant_values', 'value_en')
                    ->where('variant_type_id', $variantType->id)
                    ->ignore($variantValue->id),
            ],
            'status' => ['required', 'boolean'],
        ];

        $rules['color_code'] = $variantType->input_type === 'color'
            ? ['required', 'regex:/^#[0-9A-Fa-f]{6}$/']
            : ['nullable', 'prohibited'];

        $data = $request->validate($rules);

        if ($variantType->input_type !== 'color') {
            $data['color_code'] = null;
        }

        $variantValue->update($data);

        return redirect()
            ->route('admin.variant-types.edit', $variantType)
            ->with('success', __('dashboard.variant_types.value_updated'));
    }

    public function destroyValue(
        VariantType $variantType,
        VariantValue $variantValue
    ) {
        abort_unless(
            $variantValue->variant_type_id === $variantType->id,
            404
        );

        $variantValue->delete();

        return redirect()
            ->route('admin.variant-types.edit', $variantType)
            ->with('success', __('dashboard.variant_types.value_deleted'));
    }
}
