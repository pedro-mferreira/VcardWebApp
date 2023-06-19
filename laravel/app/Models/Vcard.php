<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vcard extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'phone_number';
    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'phone_number',
        'name',
        'email',
        'password',
        'photo_url',
        'confirmation_code',
        'blocked',
        'balance',
        'max_debit',
        'piggy_bank',
        'active_piggy_bank',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'vcard');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'vcard');
    }

    

}
