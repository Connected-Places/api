<?php

namespace App\Http\Requests\CollectionOrganisationEvent;

use App\Models\Collection;
use App\Models\File;
use App\Models\Taxonomy;
use App\Rules\FileIsMimeType;
use App\Rules\FileIsPendingAssignment;
use App\Rules\RootTaxonomyIs;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user()->isSuperAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'intro' => ['required', 'string', 'min:1', 'max:500'],
            'order' => ['required', 'integer', 'min:1', 'max:' . (Collection::organisationEvents()->count() + 1)],
            'enabled' => ['required', 'boolean'],
            'sideboxes' => ['present', 'array', 'max:3'],
            'sideboxes.*' => ['array'],
            'sideboxes.*.title' => ['required_with:sideboxes.*', 'string'],
            'sideboxes.*.content' => ['required_with:sideboxes.*', 'string'],
            'category_taxonomies' => ['present', 'array'],
            'category_taxonomies.*' => ['string', 'exists:taxonomies,id', new RootTaxonomyIs(Taxonomy::NAME_CATEGORY)],
            'image_file_id' => [
                'required',
                'exists:files,id',
                new FileIsMimeType(File::MIME_TYPE_PNG, File::MIME_TYPE_JPG, File::MIME_TYPE_SVG),
                new FileIsPendingAssignment(),
            ],
        ];
    }
}