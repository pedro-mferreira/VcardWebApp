<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'vcard',
        'date',
        'datetime',
        'type',
        'value',
        'old_balance',
        'new_balance',
        'payment_type',
        'payment_reference',
        'pair_transaction',
        'pair_vcard',
        'category_id',
        'description',
    ];

    public function vcardDetail()
    {
        return $this->belongsTo(Vcard::class, 'vcard');
    }

    public function category_idDetail(){
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function pair_vcardDetail()
    {
        return $this->belongsTo(Vcard::class, 'pair_vcard');
    }

    public function payment_typeDetail(){
        return $this->belongsTo(Payment_type::class, 'payment_type');
    }

}
