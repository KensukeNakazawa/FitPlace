@extends('layouts.header')
@section('content')
  <div class="container">
    <h1>管理者 一覧</h1>

    <button type="button" class="btn btn-outline-secondary">
      <a href={{ route('admin.create') }}>
        追加
      </a>
    </button>
    <div class="table-responsive">
      <table class="table">
        <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">管理者名</th>
          <th scope="col">ログインID</th>
          <th scope="col">権限ID</th>
          <th scope="col">作成日</th>
          <th scope="col">最終更新日</th>
          <th scope="col">削除</th>
        </tr>
        </thead>
        <tbody>
        @foreach($admins as $admin)
          <tr>
            <td>{{ $admin->id }}</td>
            <td>{{ $admin->name }}</td>
            <td>{{ $admin->login_id }}</td>
            <td>{{ $admin->authority }}</td>
            <td>{{ $admin->created_at->format('Y/m/d') }}</td>
            <td>{{ $admin->updated_at->format('Y/m/d') }}</td>
            <td>
              <form class="js-delete-form" method="POST" action={{ route('admin.destroy', ['admin_id' => $admin->id]) }}>
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                  Delete
                </button>
              </form>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    {{ $admins->links() }}
  </div>

  <script>
    $('.js-delete-form').on('click', function() {
      if (!window.confirm('本当に削除しますか？')) {
        return false;
      }
    });
  </script>

@endsection
