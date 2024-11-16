@extends('layouts.app')
@section('content')
    <!-- CONTAINER -->
    <div class="container d-flex align-items-center min-vh-100">
        <div class="row g-0 justify-content-center">
            <!-- TITLE -->
            <div class="px-0 mx-0 col-lg-4 offset-lg-1">
                <div id="title-container">
                    <img class="covid-image rounded-circle" src="{{ asset('images/perfume-bottle2.jpg') }}">
                    <h2>Perfume</h2>
                    <h3>Get your desired perfume</h3>
                    <p>Answer carefully and make your adjustable perfume!</p>
                    @include('components.language-switch')
                </div>
            </div>
            <!-- FORMS -->
            <div class="px-0 mx-0 col-lg-7">
                <div class="progress">
                    <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 0%"></div>
                </div>
                <div id="qbox-container">

                    <!-- Suggested Products Section -->
                    @if(isset($suggestedProducts) && (is_array($suggestedProducts) ? count($suggestedProducts) > 0 : $suggestedProducts->isNotEmpty()))
                        <div class="mt-5">
                            <!-- Success Message -->
                            @if(session('success'))
                                <div class="mb-4 alert alert-success d-flex justify-content-between align-items-center" role="alert">
                                    <span class="font-weight-bold">{{ session('success') }}</span>
                                    <button type="button" class="btn-close" aria-label="Close" onclick="this.parentElement.remove()"></button>
                                </div>
                            @endif
                            <h3>Suggested Products</h3>
                            <div class="row">
                                @foreach($suggestedProducts as $product)
                                    <div class="mb-3 col-md-4">
                                        <div class="card">
                                            <img src="{{ asset('storage/'.$product->photos[0]) }}" class="card-img-top" alt="{{ $product->name }}">
                                            <div class="card-body">
                                                <h7 class="card-title">{{ $product->name }}</h7>
                                                
                                                <!-- View Details Button -->
                                                <a href="#" class="btn btn-danger" style="margin-top: 5px" onclick="toggleDescription(event, 'description-{{ $product->id }}')">View Details</a>

                                                <!-- Product Description -->
                                                <div id="description-{{ $product->id }}" class="product-description d-none">
                                                    {!! $product->description !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                <a href="{{route('quiz')}}">Not Like! Make Again</a>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <!-- PRELOADER -->
    <div id="preloader-wrapper">
        <div id="preloader"></div>
        <div class="preloader-section section-left"></div>
        <div class="preloader-section section-right"></div>
    </div>
    
    <script>
    function toggleDescription(event, descriptionId) {
        event.preventDefault(); // Prevent page reload on button click
        const descriptionElement = document.getElementById(descriptionId);
        
        if (descriptionElement.classList.contains('d-none')) {
            descriptionElement.classList.remove('d-none');
        } else {
            descriptionElement.classList.add('d-none');
        }
    }
</script>

@endsection

