<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\ItemType; // ItemType モデルを追加

class ItemController extends Controller
{
    // コンストラクターでモデルのインスタンスを作成する必要はありません

    /**
     * 商品一覧
     */
    public function index()
    {
        // ユーザー情報取得
        $user = Auth::user();

        // 商品一覧取得
        // with メソッドを使って ItemType モデルを事前に読み込む
        $items = Item::with('itemType')->get();
        foreach ($items->all() as $item) {
            $item->type = $items->find($item->type)->itemType->name;
            $items[$items->find($item->id)->id - 1] = $item;
        }

        return view('item.index', compact('user', 'items'));
    }

    /**
     * 商品検索
     */
    public function search(Request $request)
    {
        // ユーザー情報取得
        $user = Auth::user();

        // 検索ワードに基づいて商品を検索
        $keyword = $request->input('keyword');
        // 検索ワードが空欄の場合、絞り込みはしない
        if (is_null($keyword)) {
            $items = Item::all();
        } else {
            $items = Item::where(
                function ($query) use ($keyword) {
                    $query->where('name', 'like', "%$keyword%")
                        ->orWhere('detail', 'like', "%$keyword%");
                }
            )->orWhereHas(
                    'itemType',
                    function ($query) use ($keyword) {
                        $query->where('name', 'like', "%$keyword%");
                    }
                )->get();
        }

        return view('item.index', compact('user', 'items'));
    }


    /**
     * 商品登録
     */
    public function add(Request $request)
    {
        $user = Auth::user();

        if (Gate::allows('view-user', $user)) {
            // 管理者アカウントの場合
            // POSTリクエストのとき
            if ($request->isMethod('post')) {
                // バリデーション
                $this->validate($request, [
                    'name' => 'required|max:100',
                    'type' => 'required|max:100',
                ]);

                // 種別登録
                $itemType = ItemType::firstOrCreate(['name' => $request->type]);

                // 商品登録
                Item::create([
                    'user_id' => Auth::user()->id,
                    'name' => $request->name,
                    'type' => $itemType->id,
                    'detail' => $request->detail,
                ]);

                return redirect('/items');
            }

            return view('item.add');
        } else {
            // 一般アカウントの場合
            return redirect('/items');
        }
    }

    /**
     * 商品編集
     */
    public function edit(Request $request)
    {
        $user = Auth::user();

        if (Gate::allows('view-user', $user)) {
            // 管理者アカウントの場合
            // POSTリクエストのとき
            if ($request->isMethod('post')) {
                // バリデーション
                $this->validate($request, [
                    'name' => 'required|max:100',
                    'type' => 'required|max:100',
                ]);

                // 種別登録
                $itemType = ItemType::firstOrCreate(['name' => $request->type]);

                // データを更新
                $item = Item::find($request->id);
                $item->update([
                    'name' => $request->name,
                    'type' => $itemType->id,
                    'detail' => $request->detail
                ]);

                return redirect('/items');
            } else if ($request->isMethod('get')) {
                // GETリクエストのとき
                $items = Item::with('itemType')->get();
                $item = $items->find($request->item_id);
                $item->type = $item->itemType->name;
                return view('item.edit', compact('item'));
            }
        } else {
            // 一般アカウントの場合
            return redirect('/items');
        }
    }
}
