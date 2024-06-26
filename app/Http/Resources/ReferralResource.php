<?php

namespace App\Http\Resources;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

class ReferralResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @throws \Exception
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'reference' => $this->reference,
            'status' => $this->status,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'other_contact' => $this->other_contact,
            'postcode_outward_code' => $this->postcode_outward_code,
            'comments' => $this->comments,
            'referral_consented_at' => $this->referral_consented_at?->format(CarbonImmutable::ISO8601),
            'feedback_consented_at' => $this->feedback_consented_at?->format(CarbonImmutable::ISO8601),
            'referee_name' => $this->referee_name,
            'referee_email' => $this->referee_email,
            'referee_phone' => $this->referee_phone,
            'referee_organisation' => $this->organisationTaxonomy->name ?? $this->organisation,
            'created_at' => $this->created_at->format(CarbonImmutable::ISO8601),
            'updated_at' => $this->updated_at->format(CarbonImmutable::ISO8601),

            // Relationships.
            'service' => new ServiceResource($this->whenLoaded('service')),

            // Appends.
            'status_last_updated_at' => $this->when(
                isset($this->status_last_updated_at),
                function () {
                    return Date::createFromFormat(
                        'Y-m-d H:i:s',
                        $this->status_last_updated_at
                    )->format(CarbonImmutable::ISO8601);
                }
            ),
        ];
    }
}
