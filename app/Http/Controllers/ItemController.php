<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\ItemType;

class ItemController extends Controller
{
    protected $items;
    protected $item_types;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->items = new Item;
        $this->item_types = new ItemType;
    }

    /**
     * 商品一覧
     */
    public function index()
    {
        // ユーザー情報取得
        $user = Auth::user();
        // 商品一覧取得
        foreach ($this->items->all() as $item) {
            $item->type = $this->item_types->find($item->type)->name;
            $items[] = $item;
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
                // テーブル item_types で該当するデータをクエリ
                $itemType = ItemType::where('name', $request->type)->first();

                // クエリの結果を確認し、該当するデータが存在するかを判定
                if ($itemType) {
                    $type_id = $itemType->id;
                } else {
                    $this->item_types->create([
                        'name' => $request->type,
                    ]);
                    $type_id = ItemType::latest()->first()->id;
                }

                // 商品登録
                Item::create([
                    'user_id' => Auth::user()->id,
                    'name' => $request->name,
                    'type' => $type_id,
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
