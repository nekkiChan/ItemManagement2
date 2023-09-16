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
        $items = Item::with('itemType')->where('status', '!=', 'delete')->get();
        // 種別ID→種別名
        $items->transform(function ($item) {
            $item->type = $item->itemType->name;
            return $item;
        });

        return view('item.index', compact('user', 'items'));
    }

    /**
     * アーカイブ一覧
     */
    public function archive()
    {
        // ユーザー情報取得
        $user = Auth::user();

        if (Gate::allows('view-user', $user)) {
            // 商品一覧取得
            // with メソッドを使って ItemType モデルを事前に読み込む
            $items = Item::with('itemType')->where('status', '=', 'delete')->get();
            $items->transform(function ($item) {
                $item->type = $item->itemType->name;
                return $item;
            });

            return view('item.archive', compact('user', 'items'));

        } else {
            // 一般アカウントの場合
            return redirect('/items/archive');
        }
    }

    /**
     * 商品検索
     */
    public function search(Request $request)
    {
        // ユーザー情報取得
        $user = Auth::user();
        // ページ取得
        $page = $request->input('page');

        // 検索タイプ
        $search_type = $request->input('search_type');
        // 検索ワードに基づいて商品を検索
        $keyword = $request->input('keyword');

        // ステータス
        $status = $request->input('status');
        // ステータスに即した商品
        $items = Item::where('status', '=', "$status");

        // 検索ワードが空欄の場合、絞り込みはしない
        if (is_null($keyword)) {
            $items = $items->get();
        } else if ($search_type == 'type') {
            // 種別検索
            $items = $items->whereHas(
                'itemType',
                function ($query) use ($keyword) {
                    $query->where('name', 'like', "%$keyword%");
                }
            )->get();
        } else if ($search_type == 'name' || $search_type == 'detail') {
            // 名前検索・詳細検索
            $items = $items->where(
                function ($query) use ($keyword, $search_type) {
                    $query->where($search_type, 'like', "%$keyword%");
                }
            )->get();
        } else {
            // 全体検索
            // $items = $items->where('status', '=', $status)->where(
            //     function ($query) use ($keyword) {
            //         $query->where('name', 'like', "%$keyword%")
            //             ->orWhere('detail', 'like', "%$keyword%");
            //     }
            // )->orWhereHas(
            //         'itemType',
            //         function ($query) use ($keyword) {
            //             $query->where('name', 'like', "%$keyword%");
            //         }
            //     )->get();
            $items = $items->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%$keyword%")
                        ->orWhereHas('itemType', function ($subquery) use ($keyword) {
                            $subquery->where('name', 'like', "%$keyword%");
                        })
                        ->orWhere('detail', 'like', "%$keyword%");
                })
                ->get();

        }

        // 種別ID→種別名
        $items->transform(function ($item) {
            $item->type = $item->itemType->name;
            return $item;
        });

        // $items->where('status', '=', $status);

        return view("item.$page", compact('user', 'items'));
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
        // ユーザー取得
        $user = Auth::user();
        // ページ取得
        $page = $request->input('page');
        // 商品のステータス取得
        $status = $request->input('status');
        $items = Item::where('status', '=', "$status")->get();

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

                // 種別ID→種別名
                $items->transform(function ($item) {
                    $item->type = $item->itemType->name;
                    return $item;
                });

                return view("item.$page", compact('user', 'items'));
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

    public function convertStatus(Request $request)
    {
        $item = new Item;
        $item->convertStatus($request->item_id);

        return redirect('/items');
    }

}
