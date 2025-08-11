<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeaponAbilityResource extends JsonResource
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
            'name' => $this->name,
            'lore' => $this->lore,
            'rules' => $this->rules,
        ];

        return $arr;
    }
}