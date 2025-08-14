<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FactionResource extends JsonResource
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
            'name' => $this->name,
            'lore' => $this->lore,
        ];

        return $arr;
    }
}