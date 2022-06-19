<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CashResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "description" => $this->description,
            "when" => $this->when->format("d F Y H:i"),
            "amount" => abs($this->amount),
            // "slug" => $this->slug,
        ];
    }
}
