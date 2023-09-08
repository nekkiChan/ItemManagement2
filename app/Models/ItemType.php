<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function items()
    {
        return $this->hasMany(Item::class, 'type');
    }

    public function searchItem($keyword)
    {
        return $this->items()
            ->where('name', 'like', "%{$keyword}%")
            ->get();
    }
}
