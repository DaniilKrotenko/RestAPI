@extends('layouts.app')

@section('content')
    <form method="POST" action="{{ route('workersSubmit') }}">
        @csrf

        <div class="container">
            <div class="row">
                <div class="col-6">

                </div>
                <div class="col-6">
                    <div class="form-group">
                        <input class="form-control mb-2" placeholder="Name" name="name">
                    </div>
                    <div class="form-group">
                        <input class="form-control mb-2" placeholder="Email" name="email">
                    </div>
                    <div class="form-group">
                        <input class="form-control mb-2" placeholder="Password" name="password">
                    </div>
                    <button class="btn btn-success">Save</button>
                </div>
            </div>
        </div>

    </form>
@endsection

