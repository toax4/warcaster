<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeaponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->withTranslation();

        $arr = [
            'id' => $this->id,
            "name" => $this->name,
            "range" => $this->range,
            "attack" => $this->attack,
            "hit" => $this->hit,
            "wound" => $this->wound,
            "rend" => $this->rend,
            "damage" => $this->damage,
            "abilities" => WeaponAbilityResource::collection($this->abilities),
        ];
        // }

        return $arr;

    }
}