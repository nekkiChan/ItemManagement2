<table class="table table-hover text-nowrap">
    <thead>
        <tr>
            <th>ID</th>
            <th>名前</th>
            <th>種別</th>
            <th>詳細</th>
            @can('view-user', $user)
                <th></th>
            @endcan
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->itemType->name }}</td>
                <td>{{ $item->detail }}</td>
                @can('view-user', $user)
                    <td>
                        <div style="height: 1em" class="d-flex align-items-center">
                            <form method="GET" action="{{ route('items.edit') }}" class="d-flex align-items-center">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                <button type="submit" class="btn btn-link p-0 m-0">
                                    <img src="{{ asset('img/edit.svg') }}" alt="" style="height: 20px;"
                                        class="mr-2">
                                </button>
                            </form>
                            <form method="GET" action="{{ route('items.convert_status') }}"
                                class="d-flex align-items-center">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                <button type="submit" class="btn btn-link p-0 m-0">
                                    <img src="{{ asset("img/$icon") }}" alt="" style="height: 20px;"
                                        class="ml-2">
                                </button>
                            </form>
                        </div>
                    </td>
                @endcan
            </tr>
        @endforeach
    </tbody>
</table>
