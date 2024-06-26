<?php

namespace App\Http\Requests\TaxonomyServiceEligibility;

use App\Models\Taxonomy;
use App\Rules\RootTaxonomyIs;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Get the new parent ID.
        $parentId = $this->parent_id ?? Taxonomy::serviceEligibility()->id;

        // Check if the parent taxonomy remained the same.
        $hasSameParent = $this->taxonomy->parent_id === $parentId;

        // Get the sibling count for the same parent.
        $siblingTaxonomiesCount = Taxonomy::where('parent_id', $parentId)->count();

        // Increment the sibling count if the parent is new.
        $siblingTaxonomiesCount = $hasSameParent ? $siblingTaxonomiesCount : $siblingTaxonomiesCount + 1;

        return [
            'parent_id' => ['present', 'nullable', 'exists:taxonomies,id', new RootTaxonomyIs(Taxonomy::NAME_SERVICE_ELIGIBILITY)],
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'order' => ['required', 'integer', 'min:1', "max:$siblingTaxonomiesCount"],
        ];
    }
}
