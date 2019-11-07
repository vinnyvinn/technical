<?php

namespace App\Http\Resources;

use App\FuelSupplier;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class FuelResource extends JsonResource
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
            'litres' => $this->litres,
            'vehicle_id' => $this->vehicle_id,
            'supplier_id' => $this->supplier_id,
            'driver_id' => $this->driver_id,
            'currency' => $this->currency,
            'expense_id' => $this->expense_id,
            'vehicle_name' => $this->vehicle_name,
            'driver_name' => $this->driver_name,
            'vehicle' => $this->vehicle_name ? $this->vehicle_name : $this->vehicle->code ,
            'fuel_type_id' => $this->fuel_type_id,
            'fuel_type' => $this->fueltype->name,
            'invoice_no' => $this->invoice_no,
            'fuel_on' => $this->fuel_on,
            'price' => $this->supplier->price,
            'current_km_readings' => $this->current_km_readings,
            'other_expenses' => $this->other_expenses,
            'cash_sale_no' => $this->cash_sale_no,
            'odometer_readings' => $this->odometer_readings,
            'previous_odometer' => $this->vehicle ? $this->vehicle->odometer_readings :'',
             ];
    }
}
