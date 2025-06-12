@extends('layouts.app')

@section('content')

<div class="container my-5" id="navigation-plantes">
  <h2 class="text-center mb-4">Explorer par grands groupes animaux</h2>

  <div class="row row-cols-1 row-cols-sm-2 g-2 justify-content-center" style="max-width: 820px; margin: 0 auto;">

    <div class="col">
      <a href="/animaux/mammiferes" class="text-decoration-none">
        <div class="position-relative" style="width: 100%; height: 400px;">
          <img src="{{ Storage::url('925661.jpg'); }}" class="img-fluid w-100 h-100 rounded shadow-sm" style="object-fit: cover;" alt="Mammifères">
          <div class="position-absolute top-50 start-50 translate-middle text-white fw-bold fs-5 text-center bg-dark bg-opacity-50 w-100 py-2">Mammifères</div>
        </div>
      </a>
    </div>

    <div class="col">
      <a href="/animaux/oiseaux" class="text-decoration-none">
        <div class="position-relative" style="width: 100%; height: 400px;">
          <img src="{{ Storage::url('2191.jpg'); }}" class="img-fluid w-100 h-100 rounded shadow-sm" style="object-fit: cover;" alt="Oiseaux">
          <div class="position-absolute top-50 start-50 translate-middle text-white fw-bold fs-5 text-center bg-dark bg-opacity-50 w-100 py-2">Oiseaux</div>
        </div>
      </a>
    </div>

    <div class="col">
      <a href="/animaux/insectes" class="text-decoration-none">
        <div class="position-relative" style="width: 100%; height: 400px;">
          <img src="{{ Storage::url('61749.jpg'); }}" class="img-fluid w-100 h-100 rounded shadow-sm" style="object-fit: cover;" alt="Insectes">
          <div class="position-absolute top-50 start-50 translate-middle text-white fw-bold fs-5 text-center bg-dark bg-opacity-50 w-100 py-2">Insectes</div>
        </div>
      </a>
    </div>



    <div class="col">
      <a href="/animaux/reptiles" class="text-decoration-none">
        <div class="position-relative" style="width: 100%; height: 400px;">
          <img src="{{ Storage::url('73972.jpg'); }}" class="img-fluid w-100 h-100 rounded shadow-sm" style="object-fit: cover;" alt="Reptiles">
          <div class="position-absolute top-50 start-50 translate-middle text-white fw-bold fs-5 text-center bg-dark bg-opacity-50 w-100 py-2">Reptiles</div>
        </div>
      </a>
    </div>

  </div>
</div>

@endsection
