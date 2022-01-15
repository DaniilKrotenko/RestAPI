@extends('layouts.app')

@section('content')
    <form method="POST" action="{{ route('newWorker') }}">
        @csrf

        <div class="container">
            <div class="row">
                <div class="col-6">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <input class="form-control mb-2" placeholder="Name" name="name">
                    </div>
                    <div class="form-group">
                        <input class="form-control mb-2" placeholder="Address" name="email">
                    </div>
                    <div class="form-group">
                        <input class="form-control mb-2" placeholder="Project number" name="password">
                    </div>
                    <div class="form-group">
                        <input class="form-control mb-2" placeholder="Project number" name="role_id">
                    </div>

                    <button class="btn btn-success">Save</button>
                </div>
            </div>
        </div>

    </form>
@endsection
