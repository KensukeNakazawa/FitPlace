@extends('layouts.header')
@section('content')
  <div class="container">
    <h1>体の部位一覧</h1>
    <div class="table-responsive">
      <table class="table">
        <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">名前</th>
          <th scope="col">インデックス番号</th>
          <th scope="col">編集</th>
        </tr>
        </thead>
        <tbody>
        @foreach($body_parts as $body_part)
          <tr>
            <td>{{ $body_part->id }}</td>
            <td>{{ $body_part->name }}</td>
            <td>{{ $body_part->index}}</td>
            <td>
              <button type="button" class="btn btn-info">
                <a href={{ route('body_part.edit', ['body_part_id' => $body_part->id]) }}>
                  編集
                </a>
              </button>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    {{ $body_parts->links() }}
  </div>
@endsection