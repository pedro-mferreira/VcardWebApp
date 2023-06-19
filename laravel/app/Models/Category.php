<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    use HasFactory;
    

    protected $fillable = [
        'vcard',
        'type',
        'name',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }

    public function vcardDetail(){
        return $this->belongsTo(Vcard::class, 'vcard');
    }
}
