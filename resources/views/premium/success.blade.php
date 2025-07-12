@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Success</div>

                <div class="card-body">
                    <h3>Thank you for upgrading to Premium!</h3>
                    <a href="{{ route('home') }}" class="btn btn-success">Go to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
