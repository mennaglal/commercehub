<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class SubCategoryRequest extends CategoryRequest
{
    // The resource route parameter is named "sub_category".
    protected function currentCategory()
    {
        return $this->route('sub_category');
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'parent_id' => [
                'required',
                Rule::exists('categories', 'id')->whereNull('parent_id'),
            ],
        ]);
    }
}
