@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('tree.vivant')
            <main class="col-md-12">

                @php
                    $categories = [
                        ['url' => '/animaux/mammiferes', 'img' => '925661.jpg', 'label' => 'Mammifères'],
                        ['url' => '/animaux/oiseaux', 'img' => '2191.jpg', 'label' => 'Oiseaux'],
                        ['url' => '/animaux/Insectes', 'img' => '61749.jpg', 'label' => 'Insectes'],
                        ['url' => '/animaux/Reptiles', 'img' => '73972.jpg', 'label' => 'Reptiles'],
                    ];
                @endphp

                <div class="container my-5" id="navigation-plantes">
                    <div class="row row-cols-1 row-cols-sm-2 g-2 justify-content-center"
                        style="max-width: 820px; margin: 0 auto;">
                        @foreach ($categories as $cat)
                            <div class="col">
                                <a href="{{ $cat['url'] }}" class="text-decoration-none">
                                    <div class="position-relative" style="width: 100%; height: 400px;">
                                        <img src="{{ Storage::url($cat['img']) }}"
                                            class="img-fluid w-100 h-100 rounded shadow-sm" style="object-fit: cover;"
                                            alt="{{ $cat['label'] }}">
                                        <div
                                            class="position-absolute top-50 start-50 translate-middle text-white fw-bold fs-5 text-center bg-dark bg-opacity-50 w-100 py-2">
                                            {{ $cat['label'] }}
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>


            </main>
        </div>
    </div>
@endsection
