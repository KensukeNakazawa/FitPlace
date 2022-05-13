@extends('layouts.header')
@section('content')
  <div class="container">
    <h1>管理者追加</h1>
    <div class="card">
      <form method="POST" action={{ route('admin.store') }}>
        @csrf
        <div class="card-text">
          <div class="mb-3">

            <label for="login_id">ログインID</label>
            <input type="text" class="form-control" id="login_id" name="login_id" value="{{ old('login_id')}}"
                   required
                   autofocus><br>
          </div>
        </div>

        <div class="card-text">
          <div class="mb-3">
            <label for="password">パスワード</label>
            <input type="password" class="form-control" name="password" required autocomplete="current-password">
          </div>
        </div>

        <div class="card-text">
          <div class="mb-3">
            <label for="name">管理者名</label>
            <input type="text" class="form-control" name="name" required>
          </div>
        </div>

        <div class="card-text">
          <div class="mb-3">
            <select name="authority" class="form-select">
              @if ($admin->authority === '1')
                <option value="1">マスター管理者</option>
                <option value="2">一般</option>
              @else
                <option value="2">一般</option>
              @endif

            </select>
          </div>
        </div>

        <button type="submit" class="btn btn-primary mb-3">追加</button>

      </form>
    </div>
  </div>
@endsection