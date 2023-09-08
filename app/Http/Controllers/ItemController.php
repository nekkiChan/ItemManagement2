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

    public function search(Request $request)
    {
        // ユーザー情報取得
        $user = Auth::user();

        // キーワードに基づいて商品を検索
        $keyword = $request->input('keyword');
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
                $itemType = ItemType::firstOrCreate(['name' => $request->type]); // 修正: firstOrCreate メソッドを使用してタイプを取得または作成

                // 商品登録
                Item::create([
                    'user_id' => Auth::user()->id,
                    'name' => $request->name,
                    'type' => $itemType->id,
                    // 修正: ItemType モデルから取得した ID を使用
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
}
