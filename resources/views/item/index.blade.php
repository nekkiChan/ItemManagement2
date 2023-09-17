@extends('adminlte::page')

@section('title', '商品一覧')

@php
    $icon = 'trash.svg';
@endphp

@section('content_header')
    <h1>商品一覧</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">商品一覧</h3>
                    <div class="card-tools">
                        <form action="{{ route('items.search') }}" method="POST" class="input-group input-group-sm">
                            @csrf
                            <input type="hidden" name="page" value="{{ $page }}">
                            <input type="hidden" name="status" value="active">
                            <select name="search_type">
                                <option value="">全体検索</option>
                                <option value="name">名前検索</option>
                                <option value="type">種別検索</option>
                                <option value="detail">詳細検索</option>
                            </select>
                            <input type="text" name="keyword" class="form-control" placeholder="キーワードを入力">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">検索</button>
                            </div>
                        </form>
                    </div><!-- /.card-tools -->
                </div><!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    @component('components.item.item-list', ['user' => $user, 'items' => $items, 'page' => $page, 'icon' => $icon])
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
@stop
