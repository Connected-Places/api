<?php

namespace App\Http\Resources;

use App\Models\Taxonomy;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxonomyCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id !== Taxonomy::category()->id ? $this->parent_id : null,
            'slug' => $this->slug,
            'name' => $this->name,
            'order' => $this->order,
            'children' => static::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at->format(CarbonImmutable::ISO8601),
            'updated_at' => $this->updated_at->format(CarbonImmutable::ISO8601),
        ];
    }
}
