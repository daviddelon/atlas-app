@extends('layouts.app')

@section('content')

<div class="container my-5" id="navigation-plantes">
  <h2 class="text-center mb-4">Explorer par grandes classes botaniques</h2>

  <div class="row row-cols-1 row-cols-sm-2 g-2 justify-content-center" style="max-width: 820px; margin: 0 auto;">

    <div class="col">
      <a href="/plantes/angiospermes" class="text-decoration-none">
        <div class="position-relative" style="width: 100%; height: 400px;">
          <img src="{{ Storage::url('202282.jpg'); }}" class="img-fluid w-100 h-100 rounded shadow-sm" style="object-fit: cover;" alt="Plantes à fleurs">
          <div class="position-absolute top-50 start-50 translate-middle text-white fw-bold fs-5 text-center bg-dark bg-opacity-50 w-100 py-2">Plantes à fleurs</div>
        </div>
      </a>
    </div>

    <div class="col">
      <a href="/plantes/gymnospermes" class="text-decoration-none">
        <div class="position-relative" style="width: 100%; height: 400px;">
          <img src="{{ Storage::url('82722.jpg'); }}" class="img-fluid w-100 h-100 rounded shadow-sm" style="object-fit: cover;" alt="Conifères">
          <div class="position-absolute top-50 start-50 translate-middle text-white fw-bold fs-5 text-center bg-dark bg-opacity-50 w-100 py-2">Conifères</div>
        </div>
      </a>
    </div>

    <div class="col">
      <a href="/plantes/fougeres" class="text-decoration-none">
        <div class="position-relative" style="width: 100%; height: 400px;">
          <img src="{{ Storage::url('346413.jpg'); }}" class="img-fluid w-100 h-100 rounded shadow-sm" style="object-fit: cover;" alt="Fougères">
          <div class="position-absolute top-50 start-50 translate-middle text-white fw-bold fs-5 text-center bg-dark bg-opacity-50 w-100 py-2">Fougères</div>
        </div>
      </a>
    </div>



    <div class="col">
      <a href="/plantes/mousses" class="text-decoration-none">
        <div class="position-relative" style="width: 100%; height: 400px;">
          <img src="{{ Storage::url('1180933.jpg'); }}" class="img-fluid w-100 h-100 rounded shadow-sm" style="object-fit: cover;" alt="Mousses">
          <div class="position-absolute top-50 start-50 translate-middle text-white fw-bold fs-5 text-center bg-dark bg-opacity-50 w-100 py-2">Mousses et hépatiques</div>
        </div>
      </a>
    </div>

  </div>
</div>

@endsection
