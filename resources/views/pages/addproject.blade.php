@extends('layouts.app')

@section('content')
    <form method="POST" action="{{ route('addproject') }}">
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
                        <input class="form-control mb-2" placeholder="Address" name="address">
                    </div>
                    <div class="form-group">
                        <input class="form-control mb-2" placeholder="Project number" name="projectNumber">
                    </div>
                    <div class="form-group" style="position:relative; left: 5%">
                        <input type="checkbox" class="form-check-input mb-4" style="position: relative;" name="geoFance">
                        <label class="form-check-label" for="exampleCheck1">Check me out</label>
                    </div>
                    <div class="form-group">
                        <input class="form-control mb-2" placeholder="Radius (m)" name="radius">
                    </div>
                    <button class="btn btn-success">Save</button>
                </div>
            </div>
        </div>

    </form>
@endsection
