@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('shifts') }}">
    @csrf

    <div class="container">
        <div class="row">
            <div class="col-6">

            </div>
            <div class="col-6">
                <div class="form-group">
                    <input class="form-control mb-2" name="user_id">
                </div>
                <div class="form-group">
                    <input class="form-control mb-2" name="shift_id">
                </div>
                <div class="form-group">
                    <input class="form-control mb-2" name="timeStart">
                </div>
                <div class="form-group">
                    <input class="form-control mb-2" name="timeEnd">
                </div>
                <div class="form-group">
                    <input class="form-control mb-2" name="date">
                </div>
                <button class="btn btn-success">Save</button>
            </div>
        </div>
    </div>

</form>
@endsection

