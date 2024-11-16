@extends('layouts.app')
@section('content')
 <!-- CONTAINER -->
 <div class="container d-flex align-items-center min-vh-100">
    <div class="row g-0 justify-content-center">
        <!-- TITLE -->
        <div class="px-0 mx-0 col-lg-4 offset-lg-1">
            <div id="title-container">
                <img class="covid-image rounded-circle" src="{{asset('images/perfume-bottle2.jpg')}}">
                <h2>Perfume</h2>
                <h3>Get your desired perfume</h3>
                <p>Answers carefully and make your adjustable perfume!</p>
                @include('components.language-switch')
            </div>
        </div>
        <!-- FORMS -->
        <div class="px-0 mx-0 col-lg-7">
            <div class="progress">
                <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 0%"></div>
            </div>
            <div id="qbox-container">
                <form action="{{route('answers.store')}}" class="needs-validation" id="form-wrapper" method="POST" name="form-wrapper">
                    @csrf
                     <!-- Success Message -->
                    @if(session('success'))
                        <div class="mb-4 alert alert-success d-flex justify-content-between align-items-center" role="alert">
                            <span class="font-weight-bold">{{ session('success') }}</span>
                            <button type="button" class="btn-close" aria-label="Close" onclick="this.parentElement.remove()"></button>
                        </div>
                    @endif

                    <div id="steps-container">
                        <!-- Gender Selection Step -->
                        <div class="step">
                            <div>
                                <h4 style="padding: 5px">Questions are ready for you!</h4>
                                <q style="font-weight:bold">Perfumi nuk njeh gjini - eshte nje gjuhe universale qe flet per shpirtin dhe stilin e cdo personi.</q>
                                <!--<img src="{{asset('images/spraying-perfume-piu-piu.gif')}}" > <br>-->
                            </div>
                            <div class="d-none">
                                <h4>
                                @if (session('language') === 'alb')
                                    Ju do të gjeni parfum për:
                                @else
                                    You are going to search perfume for:
                                @endif
                            </h4>
                            <div class="form-check ps-0 q-box">
                                <div class="q-box__question">
                                    <input class="form-check-input question__input" id="gender_male" name="gender" type="radio" value="male" required {{session('gender') == 'male' ? 'checked':'' }} onclick='return false'>
                                    <label class="form-check-label question__label" for="gender_male">Male</label>
                                </div>
                                <div class="q-box__question">
                                    <input class="form-check-input question__input" id="gender_female" name="gender" type="radio" value="female" required {{session('gender') == 'female'? 'checked' : ''}} onclick='return false'>
                                    <label class="form-check-label question__label" for="gender_female">Female</label>
                                </div>
                                <div class="q-box__question">
                                    <input class="form-check-input question__input" id="gender_other" name="gender" type="radio" value="other" required {{session('gender')== 'other'? 'checked': ''}} onclick='return false'>
                                    <label class="form-check-label question__label" for="gender_other">Other</label>
                                </div>
                            </div>
                            </div>
                        </div>
                        <!-- Dynamic Questions -->
                        @forelse ($questions as $question)
                        <div class="step">
                            <h4>
                                @if (session('language') === 'alb')
                                    {{ $question->alb_title }}
                                @else
                                    {{ $question->title }}
                                @endif
                            </h4>
                            <div class="form-check ps-0 q-box">
                                @php
                                    $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                                @endphp
                                @if (is_array($options))
                                @foreach ($options as $index => $option)
                                <div class="q-box__question">
                                    <input type="radio" name="answers[{{ $question->id }}]" id="option_{{ $question->id }}_{{ $index }}" value="{{ $option['name'] }}" class="form-check-input question__input" required>
                                    <label class="form-check-label question__label" for="option_{{ $question->id }}_{{ $index }}">
                                        <img src="{{ asset('storage/'.$option['image']) }}" alt="" width="140" height="140"> <br>
                                        {{ session('language') === 'alb' ? $option['alb_name'] : $option['name'] }}
                                    </label>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                        @empty
                        <p>Question not found!</p>
                        @endforelse

                        <!-- Final Step: Email -->
                        <div class="step d-none">
                            <label for="email" class="font-semibold">
                                @if (session('language') === 'alb')
                                    Shkruani emailin tuaj për të marrë parfumin tuaj të sugjeruar:
                                @else
                                    Enter your email to receive your suggested perfume:
                                @endif
                            </label>
                            <input type="email" name="email" id="email" class="form-control" required placeholder="your-email@example.com">
                        </div>

                        <!-- Final Message Step -->
                        <div class="step">
                            <div class="mt-1">
                                <div class="closing-text">
                                    <h4>That's about it! Stay excited!</h4>
                                    <p>ETOS AI (artificail inteligence) will assess your information and will let you know soon which will be best match for you.</p>
                                    <p>Click on the submit button to continue.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Success Message -->
                        <div id="success" class="d-none">
                            <div class="mt-5">
                                <h4>Success! You have successfull completet the quiz!</h4>
                                <p>Our ETOS AI has not found a perfume that fits your questions. Please repeat the quiz one more time.</p>
                                <a class="back-link" href="">Go back from the beginning ➜</a>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div id="q-box__buttons">
                        <button id="prev-btn" type="button">Previous</button>
                        <button id="next-btn" type="button">Next</button>
                        <button id="submit-btn" type="submit" class="d-none">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><!-- PRELOADER -->
<div id="preloader-wrapper">
    <div id="preloader"></div>
    <div class="preloader-section section-left"></div>
    <div class="preloader-section section-right"></div>
</div>

@endsection
