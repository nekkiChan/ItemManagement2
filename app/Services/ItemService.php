<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemType;

class ItemService
{
    /**
     * 商品取得
     */
    public function getActiveItems()
    {
        return Item::with('itemType')->where('status', '!=', 'delete')->get();
    }

    /**
     * アーカイブ済み商品取得
     */
    public function getDeletedItems()
    {
        return Item::with('itemType')->where('status', '=', 'delete')->get();
    }

    /**
     * 商品検索
     */
    public function searchItems($status, $searchType, $keyword)
    {
        $items = Item::where('status', $status);

        if (!is_null($keyword)) {
            $items = $this->applySearchCriteria($items, $searchType, $keyword);
        }

        return $items->get();
    }

    /**
     * 商品登録
     */
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

    /**
     * 商品更新
     */
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

    /**
     * IDから商品を取得
     */
    public function getItemById($itemId)
    {
        return Item::with('itemType')->find($itemId);
    }

    /**
     * ステータスから商品を取得
     */
    public function getItemsByStatus($status)
    {
        return Item::with('itemType')->where('status', $status)->get();
    }

    /**
     * IDからステータスを取得
     */
    public function getStatusById($id)
    {
        return Item::where('id', '=', $id)->first()->status;
    }


    /**
     * IDからページ名を取得
     */
    public function getPageNameById($id)
    {
        if ($this->getStatusById($id) == 'active') {
            return 'index';
        } elseif ($this->getStatusById($id) == 'delete') {
            return 'archive';
        }
    }


    /**
     * ステータス変換
     */
    public function convertItemStatus($itemId)
    {
        $item = Item::find($itemId);
        $item->convertStatus($itemId);
    }

    /**
     * 絞り込み検索
     */
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
