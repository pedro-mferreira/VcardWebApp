<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vcard' => new VcardResource($this->vcardDetail),
            'date' => $this->date,
            'datetime' => $this->datetime,
            'type' => $this->type,
            'value' => $this->value,
            'old_balance' => $this->old_balance,
            'new_balance' => $this->new_balance,
            'payment_type' => new Payment_TypeResource($this->payment_typeDetail),
            'payment_reference' => $this->payment_reference,
            'pair_transaction' => $this->pair_transaction,
            'pair_vcard' => new VcardResource($this->pair_vcardDetail),
            'category_id' => new CategoryResource($this->category_idDetail),
            'description' => $this->description,
        ];
    }
}
