<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VariantTypeRequest;
use App\Http\Requests\Admin\VariantValueRequest;
use App\Models\VariantType;
use App\Models\VariantValue;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class VariantTypeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:variant-types.view', only: ['index']),
            new Middleware('permission:variant-types.create', only: ['create', 'store', 'storeValue']),
            new Middleware('permission:variant-types.update', only: ['edit', 'update', 'updateValue']),
            new Middleware('permission:variant-types.delete', only: ['destroy', 'destroyValue']),
        ];
    }


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

    public function store(VariantTypeRequest $request)
    {
        $data = $request->validated();

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

    public function update(VariantTypeRequest $request, VariantType $variantType)
    {
        $data = $request->validated();

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

    public function storeValue(VariantValueRequest $request, VariantType $variantType)
    {
        $data = $request->validated();

        if ($variantType->input_type !== 'color') {
            $data['color_code'] = null;
        }

        $variantType->values()->create($data);

        return redirect()
            ->route('admin.variant-types.edit', $variantType)
            ->with('success', __('dashboard.variant_types.value_created'));
    }

    public function updateValue(
        VariantValueRequest $request,
        VariantType $variantType,
        VariantValue $variantValue
    ) {
        abort_unless($variantValue->variant_type_id === $variantType->id, 404);

        $data = $request->validated();

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
