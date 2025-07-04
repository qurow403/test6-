@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}?v={{ time() }}">
@endsection

@section('title', '勤怠詳細画面(一般ユーザー)')

@section('content')
<div class="container">
    <h1 class="title">勤怠詳細</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('attendance.update', ['id' => $attendance->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>名前</label>
            <input type="text" class="form-control" value="{{ $attendance->user->name }}" readonly>
        </div>

        <div class="form-group">
            <label>日付</label>
            <input type="date" class="form-control" value="{{ $attendance->date }}" readonly>
        </div>

        <div class="form-group">
            <label>出勤・退勤</label>
            <div class="d-flex gap-2">
                <input type="time" name="clock_in" class="form-control"
                    value="{{ old('clock_in', $attendance->clock_in ? date('H:i', strtotime($attendance->clock_in)) : '') }}">
                <span class="mx-2">〜</span>
                <input type="time" name="clock_out" class="form-control"
                    value="{{ old('clock_out', $attendance->clock_out ? date('H:i', strtotime($attendance->clock_out)) : '') }}">
            </div>
        </div>

        <!-- 任意の休憩時間入力フィールド -->
        <div class="form-group mt-3">
            <label>休憩</label>
            <div id="breaks-container">
                @php
                    $breaks = old('breaks', $attendance->breaks ?? []);
                @endphp
                @foreach ($breaks as $index => $break)
                    <div class="break-group d-flex gap-2 mb-2">
                        <input type="time" name="breaks[{{ $index }}][start]" class="form-control"
                            value="{{ isset($break['start']) ? $break['start'] : '' }}">
                        <span class="mx-2">〜</span>
                        <input type="time" name="breaks[{{ $index }}][end]" class="form-control"
                            value="{{ isset($break['end']) ? $break['end'] : '' }}">
                        <button type="button" class="btn btn-danger btn-sm remove-break">削除</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-break" class="btn btn-primary btn-sm mt-2">休憩を追加</button>
        </div>

        <div class="form-group mt-3">
            <label>備考 <span class="text-danger">*</span></label>
            <textarea name="note" class="form-control">{{ old('note', $attendance->note) }}</textarea>
        </div>

        <div class="form-group mt-3">
            <a href="{{ route('attendance.pending', ['id' => $attendance->id]) }}">修正</a>
            <a href="{{ route('attendance.index') }}" class="btn btn-secondary">戻る</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('breaks-container');
        const addBtn = document.getElementById('add-break');

        function bindRemove(button) {
            button.addEventListener('click', function () {
                this.parentNode.remove();
            });
        }

        // 初期の削除ボタンにバインド
        document.querySelectorAll('.remove-break').forEach(bindRemove);

        addBtn.addEventListener('click', function () {
            const index = container.children.length;

            const div = document.createElement('div');
            div.className = 'break-group d-flex gap-2 mb-2';
            div.innerHTML = `
                <input type="time" name="breaks[${index}][start]" class="form-control">
                <span class="mx-2">〜</span>
                <input type="time" name="breaks[${index}][end]" class="form-control">
                <button type="button" class="btn btn-danger btn-sm remove-break">削除</button>
            `;

            container.appendChild(div);
            bindRemove(div.querySelector('.remove-break'));
        });
    });
</script>
@endsection