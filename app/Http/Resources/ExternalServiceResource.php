<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExternalServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'job_card_id' => $this->job_card_id,
            'service_provider_id' => $this->service_provider_id,
            'supplier_id' => $this->supplier_id,
            'service_type' => $this->service_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'description' => $this->description,
            'item' => $this->item,
            'cost' => $this->cost,
            'quantity' => $this->quantity,
            'job_card' => $this->job_card->card_no,
            'vehicle' => $this->job_card->machine->code,
            'make' => $this->job_card->machine->make,
            'supplier' => $this->service_type=='parts' ? $this->supplier->name : $this->service_provider->name
        ];
    }
}
