@extends('adminlte::page')

@section('title', '商品一覧')

@section('content_header')
    <h1>商品一覧</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">商品一覧</h3>
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
                                @can('view-user', $user)
                                    <div class="col-4">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-append">
                                                <a href="{{ url('items/add') }}" class="btn btn-default">商品登録</a>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                @endcan
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->type }}</td>
                                            <td>{{ $item->detail }}</td>
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
