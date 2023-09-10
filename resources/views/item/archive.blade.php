@extends('adminlte::page')

@section('title', '商品一覧')

@section('content_header')
    <h1>アーカイブ一覧</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">アーカイブ一覧</h3>
                    @can('view-user', $user)
                        <div class="card-tools row">
                            <div class="col-8">
                            @else
                                <div class="card-tools">
                                    <div>
                                    @endcan
                                    <form action="{{ route('items.search') }}" method="GET"
                                        class="input-group input-group-sm">
                                        <input type="text" name="keyword" class="form-control" placeholder="キーワードを入力">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default">検索</button>
                                        </div>
                                    </form>
                                </div>
                            </div><!-- /.card-tools -->
                        </div><!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>名前</th>
                                        <th>種別</th>
                                        <th>詳細</th>
                                            <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->type }}</td>
                                            <td>{{ $item->detail }}</td>
                                                <td>
                                                    <div style="height: 1em" class="d-flex align-items-center">
                                                        <form method="GET" action="{{ route('items.edit') }}"
                                                            class="d-flex align-items-center">
                                                            @csrf
                                                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                            <button type="submit" class="btn btn-link p-0 m-0">
                                                                <img src="{{ asset('img/edit.svg') }}" alt=""
                                                                    style="height: 20px;" class="mr-2">
                                                            </button>
                                                        </form>
                                                        <form method="GET" action="{{ route('items.convert_status') }}"
                                                            class="d-flex align-items-center">
                                                            @csrf
                                                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                            <button type="submit" class="btn btn-link p-0 m-0">
                                                                <img src="{{ asset('img/plus.svg') }}" alt=""
                                                                    style="height: 20px;" class="ml-2">
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @stop

        @section('css')
        @stop

        @section('js')
        @stop
