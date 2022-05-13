@extends('layouts.header')
@section('content')
  <div class="container">
    <h1>トレーニング詳細</h1>

    <h2>{{ $exercise->exerciseType->name }}</h2>
    <h3>{{ $exercise->exerciseType->max_weight }}</h3>

    <div class="card">
        1RM {{ $exercise->max }}<br>
        トレーニングボリューム　{{ $exercise->volume }}
      <table class="table">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Rep</th>
            <th scope="col">重さ(Kg)</th>
          </tr>
        </thead>
        <tbody>
        @foreach( $exercise->getExerciseDetail() as $exercise_detail)
          <tr>
            <td>{{ $exercise_detail->id }}</td>
            <td>{{ $exercise_detail->rep }}</td>
            <td>{{ $exercise_detail->weight }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection
