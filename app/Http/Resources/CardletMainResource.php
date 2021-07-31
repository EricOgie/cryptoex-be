<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CardletMainResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => (string) $this->id,
            'cardlet' => [
                'uuid' => $this->uuid,
                'name' => $this->name,
                'type' => $this->type,
                'rate' => $this->rate,
                'code' => $this->code,
                'satus' => $this->status,
                'comment' => $this->comment,
                'owner' => $this->user
            ],

        ];
    }
}
