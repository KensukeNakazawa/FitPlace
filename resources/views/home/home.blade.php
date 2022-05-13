@extends('layouts.header')
@section('content')
  <div class="container">
    <h1>ユーザー 一覧</h1>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">ユーザー名</th>
            <th scope="col">メールアドレス</th>
            <th scope="col">誕生日</th>
            <th scope="col">作成日</th>
            <th scope="col">LineNotify</th>
            <th scope="col">詳細</th>
          </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
          <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->auth->email }}</td>
            <td>{{ $user->birth_day->format('Y/m/d') }}</td>
            <td>{{ $user->created_at->format('Y/m/d') }}</td>
            <td>
              @if ($user->lineNotify)
                <i class="fas fa-check"></i>
              @endif
            </td>
            <td>
              <button type="button" class="btn btn-info">
                <a href={{ route('user.show', ['user_id' => $user->id]) }}>
                  詳細
                </a>
              </button>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection