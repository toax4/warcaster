<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhaseResource extends JsonResource
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
            'name' => $this->name,
            'hexcolor' => $this->hexcolor,
            'displayOrder' => $this->displayOrder,
        ];

        return $arr;
    }
}