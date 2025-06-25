@php
  $categories = [
    ['url' => '/plantes/angiospermes/orchidees', 'img' => '59339.jpg', 'label' => 'Orchidées'],
    ['url' => '/plantes/angiospermes/lamiacees', 'img' => '55459.jpg', 'label' => 'Lamiacées'],
    ['url' => '/plantes/angiospermes/asteracees', 'img' => '52588.jpg', 'label' => 'Asteracées'],
  ];
@endphp

<div class="container-fluid bg-light border-top border-bottom py-3 mb-4">
  <div class="container">
    <div class="d-flex justify-content-center flex-wrap gap-3">
      @foreach($categories as $cat)
        @php
          $isActive = request()->is(trim($cat['url'], '/') . '*');
        @endphp

        <a href="{{ $cat['url'] }}" class="text-decoration-none" style="width: 120px;">
          <div class="rounded shadow-sm overflow-hidden position-relative border {{ $isActive ? 'border-success border-4' : 'border-0' }}" style="aspect-ratio: 1 / 1;">
            <img src="{{ Storage::url($cat['img']) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $cat['label'] }}">
            <div class="position-absolute top-50 start-50 translate-middle text-white fw-bold small text-center bg-dark bg-opacity-50 w-100 py-1">
              {{ $cat['label'] }}
            </div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</div>
