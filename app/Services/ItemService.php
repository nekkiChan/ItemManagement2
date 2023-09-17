<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemType;

class ItemService
{
    public function getActiveItems()
    {
        return Item::with('itemType')->where('status', '!=', 'delete')->get();
    }

    public function getDeletedItems()
    {
        return Item::with('itemType')->where('status', '=', 'delete')->get();
    }

    public function searchItems($status, $searchType, $keyword)
    {
        $items = Item::where('status', $status);

        if (!is_null($keyword)) {
            $items = $this->applySearchCriteria($items, $searchType, $keyword);
        }

        return $items->get();
    }

    public function addItem($data)
    {
        $itemType = ItemType::firstOrCreate(['name' => $data['type']]);
        Item::create([
            'user_id' => auth()->user()->id,
            'name' => $data['name'],
            'type' => $itemType->id,
            'detail' => $data['detail'],
        ]);
    }

    public function updateItem($data)
    {
        $itemType = ItemType::firstOrCreate(['name' => $data['type']]);
        $item = Item::find($data['id']);
        $item->update([
            'name' => $data['name'],
            'type' => $itemType->id,
            'detail' => $data['detail'],
        ]);
    }

    public function getItemById($itemId)
    {
        return Item::with('itemType')->find($itemId);
    }

    public function convertItemStatus($itemId)
    {
        $item = Item::find($itemId);
        $item->convertStatus($itemId);
    }

    private function applySearchCriteria($query, $searchType, $keyword)
    {
        if ($searchType == 'type') {
            $query->whereHas(
                'itemType',
                function ($query) use ($keyword) {
                    $query->where('name', 'like', "%$keyword%");
                }
            );
        } elseif (in_array($searchType, ['name', 'detail'])) {
            $query->where($searchType, 'like', "%$keyword%");
        } else {
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%")
                    ->orWhereHas('itemType', function ($subquery) use ($keyword) {
                        $subquery->where('name', 'like', "%$keyword%");
                    })
                    ->orWhere('detail', 'like', "%$keyword%");
            });
        }

        return $query;
    }
}
