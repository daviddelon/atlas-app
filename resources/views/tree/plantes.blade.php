@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <main class="col-md-12">

                @php
                    $locationSlug = config('app.default_commune_location');
                    $categories = [
                        ['url' => "/$locationSlug/plantes/angiospermes", 'img' => '202282.jpg', 'label' => 'Plantes à fleurs'],
                        ['url' => "/$locationSlug/plantes/gymnospermes", 'img' => '82722.jpg', 'label' => 'Conifères'],
                        ['url' => "/$locationSlug/plantes/fougeres", 'img' => '346413.jpg', 'label' => 'Fougères'],
                        ['url' => "/$locationSlug/plantes/mousses", 'img' => '1180933.jpg', 'label' => 'Mousses et hépatiques'],
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
