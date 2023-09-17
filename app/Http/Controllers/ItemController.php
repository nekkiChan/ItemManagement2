<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\ItemService;

class ItemController extends Controller
{
    protected $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $page = 'index';
        $items = $this->itemService->getActiveItems();

        return view('item.index', compact('user', 'items', 'page'));
    }

    public function archive(Request $request)
    {
        $user = Auth::user();
        $page = 'archive';

        if (Gate::allows('view-user', $user)) {
            $items = $this->itemService->getDeletedItems();
        } else {
            return redirect('/items/archive');
        }

        return view('item.archive', compact('user', 'items', 'page'));
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        $page = $request->input('page');
        $status = $request->input('status');
        $keyword = $request->input('keyword');
        $searchType = $request->input('search_type');

        $items = $this->itemService->searchItems($status, $searchType, $keyword);

        return view("item.$page", compact('user', 'items', 'page'));
    }

    public function add(Request $request)
    {
        $user = Auth::user();

        if (Gate::allows('view-user', $user)) {
            if ($request->isMethod('post')) {
                $this->validate($request, [
                    'name' => 'required|max:100',
                    'type' => 'required|max:100',
                ]);

                $this->itemService->addItem($request->all());

                return redirect('/items');
            }

            return view('item.add');
        } else {
            return redirect('/items');
        }
    }

    public function edit(Request $request)
    {
        $user = Auth::user();
        $page = $request->input('page');
        $status = $request->input('status');
        $items = $this->itemService->getItemsByStatus($status);

        if (Gate::allows('view-user', $user)) {
            if ($request->isMethod('post')) {
                $this->validate($request, [
                    'name' => 'required|max:100',
                    'type' => 'required|max:100',
                ]);

                $this->itemService->updateItem($request->all());

                $items = $this->itemService->getItemsByStatus($status);

                return view("item.$page", compact('user', 'items', 'page'));
            } elseif ($request->isMethod('get')) {
                $item = $this->itemService->getItemById($request->item_id);

                return view('item.edit', compact('item'));
            }
        } else {
            return redirect('/items');
        }
    }

    public function convertStatus(Request $request)
    {
        $this->itemService->convertItemStatus($request->item_id);
        $page = $request->page;

        return redirect(route("items.$page"));
    }
}
