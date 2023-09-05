<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 商品一覧
     */
    public function index()
    {
        // 商品一覧取得
        $items = Item::all();
        $user = Auth::user();

        return view('item.index', compact('items', 'user'));
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
                ]);

                // 商品登録
                Item::create([
                    'user_id' => Auth::user()->id,
                    'name' => $request->name,
                    'type' => $request->type,
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
