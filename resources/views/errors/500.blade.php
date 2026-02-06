@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Server Error</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h3>500 - Internal Server Error</h3>
                        <p class="text-muted">Something went wrong on our end. Please try again later.</p>
                    </div>
                    
                    @if(isset($message))
                        <div class="alert alert-info">
                            <strong>Error Details:</strong> {{ $message }}
                        </div>
                    @endif
                    
                    <div class="text-center">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Go Back
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
