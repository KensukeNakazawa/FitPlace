@extends('layouts.header')
@section('content')
  <div class="container">
    <h1>トレーニング 一覧</h1>
    <div class="table-responsive">
      <table class="table">
        <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">ユーザー名</th>
          <th scope="col">トレーニング名</th>
          <th scope="col">セット数</th>
          <th scope="col">ボリューム</th>
          <th scope="col">トレーニング日</th>
          <th scope="col">詳細</th>
        </tr>
        </thead>
        <tbody>
        @foreach($exercises as $exercise)
          <tr>
            <td>{{ $exercise->id }}</td>
            <td>{{ $exercise->user->name }}({{ $exercise->user->id }})</td>
            <td>{{ $exercise->exerciseType->name }}</td>
            <td>{{ $exercise->set }}</td>
            <td>{{ $exercise->volume }}</td>
            <td>{{ $exercise->exercise_at->format('Y/m/d') }}</td>
            <td>
              <button type="button" class="btn btn-info">
                <a href={{ route('exercise.show', ['exercise_id' => $exercise->id]) }}>
                  詳細
                </a>
              </button>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    {{ $exercises->links() }}
  </div>
@endsection