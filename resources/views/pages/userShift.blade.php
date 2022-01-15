
@extends('layouts.app')

@section('content')

<form method="post" action="{{ route('openShift') }}">
    @csrf

    <div class="container">
        <div class="row">
            <div class="col-6">

            </div>
            <div class="col-6">
                <button type="submit">Save</button>
            </div>
        </div>
    </div>

</form>
@endsection

