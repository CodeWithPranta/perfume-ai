@extends('layouts.app')
@section('content')
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="row g-0 justify-content-center w-100">
        <div class="px-0 mx-0 mb-5 text-center">
            <div id="title-container" class="text-center mb-5">
                <img class="covid-image rounded-circle mb-3" src="{{asset('images/perfume-bottle2.jpg')}}" alt="Perfume Image" style="max-width: 100px;">
                <h2 class="mb-2">Perfume</h2>
                <h3 class="mb-3">Get your desired perfume</h3>
                <p>Answer carefully to build your custom perfume!</p>
                @include('components.language-switch')
            </div>
        </div>
        
        <div class="px-0 mx-0" style="max-width: 500px;">
            <form action="{{ route('select-gender') }}" method="POST" class="p-4 border rounded shadow-sm">
                @csrf
                <div class="form-group mb-4 d-none">
                    <label for="gender" class="form-label">Select Gender</label>
                    <select name="gender" id="gender" class="form-control" required>
                        <option value="">Choose...</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other" selected>Other</option>
                    </select>
                </div>
                <button type="submit" class="btn mt-3 btn-danger w-100">Start Quiz</button>
            </form>
        </div>
    </div>
</div>
@endsection
