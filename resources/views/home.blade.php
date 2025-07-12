@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                @if(session('success'))
    <div style="background: #d1e7dd; padding: 10px; border-left: 4px solid green; margin-bottom: 10px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: #f8d7da; padding: 10px; border-left: 4px solid red; margin-bottom: 10px;">
        {{ session('error') }}
    </div>
@endif

                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if(auth()->user()->is_premium)
                        <h3>Welcome, Premium User!</h3>
                    @else
                        <h3>Welcome, Free User!</h3>
                        <a href="{{ route('premium.checkout') }}" class="btn btn-primary">Upgrade to Premium ($1)</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
