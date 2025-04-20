@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
    <div class="text-center">
        <h1 class="mt-5">Welcome to <?php echo config('app.name'); ?></h1>
        <p class="lead">A lightweight PHP MVC framework</p>
        
        <div class="row mt-5">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Simple & Fast</h5>
                        <p class="card-text">Designed with performance in mind, the framework is lightweight and fast.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">MVC Architecture</h5>
                        <p class="card-text">Built on the Model-View-Controller pattern for clean, maintainable code.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Intuitive Design</h5>
                        <p class="card-text">Easy to learn and use, with a Laravel-inspired syntax and structure.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-5">
            <a href="https://github.com/yourusername/wafwork" target="_blank" class="btn btn-outline-primary">View on GitHub</a>
        </div>
    </div>
@endsection 