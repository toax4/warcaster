<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
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
            'move' => $this->move,
            'save' => $this->save,
            'control' => $this->control,
            'health' => $this->health,
            'points' => $this->points,
            'bannerImage' => $this->bannerImage,
            'rowImage' => $this->rowImage,
            'name' => $this->name,
            'subname' => $this->subname,
            'lore' => $this->lore,
            'keywords' => KeywordResource::collection($this->keywords)
        ];

        return $arr;
    }
}