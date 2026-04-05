<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'description'   => $this->description,
            'amount'        => $this->amount,
            'confirm_sale'  => $this->confirm_sale,
            'shopping_cart' => $this->shopping_cart,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'user_id'       => $this->user_id,
            'product_id'    => $this->product_id,
            'sale_total'    => $this->sale_total,
            'product'       => $this->product, 
            'bold_order_id' => $this->bold_order_id
        ];
    }
}
