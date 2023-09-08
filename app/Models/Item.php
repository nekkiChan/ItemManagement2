<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ItemType;

class Item extends Model
{
    // コンストラクターは不要です

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'detail',
    ];

    public function itemType()
    {
        return $this->belongsTo(ItemType::class, 'type');
    }

    public function searchItem($keyword, $column = null)
    {
        $query = $this->newQuery();

        if (is_null($column)) {
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('detail', 'like', "%{$keyword}%");
            });
        } else if ($column == "type") {
            $query->whereHas('itemType', function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            });
        } else {
            $query->where($column, 'like', "%{$keyword}%");
        }

        return $query->get();
    }
}
