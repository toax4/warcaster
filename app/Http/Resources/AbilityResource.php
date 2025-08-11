<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbilityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->withTranslation($request->get('lang'));

        $arr = [
            'id' => $this->id,
            'cp_cost' => $this->cp_cost,
            'points' => $this->points,
            'name' => $this->name,
            'lore' => $this->lore,
            'declare' => $this->declare,
            'effect' => $this->effect,
        ];

        return $arr;
    }
}