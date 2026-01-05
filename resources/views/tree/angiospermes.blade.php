@php
  $categories = $categories ?? [
    ['url' => '/plantes/angiospermes/orchidees', 'img' => '59339.jpg', 'label' => 'Orchidées'],
    ['url' => '/plantes/angiospermes/lamiacees', 'img' => '55459.jpg', 'label' => 'Lamiacées'],
    ['url' => '/plantes/angiospermes/asteracees', 'img' => '52588.jpg', 'label' => 'Asteracées'],
  ];
  $categoriesChunks = collect($categories)->chunk(7);
@endphp

<div class="container-fluid bg-light py-3 mb-4">
  <div class="container">
    <div id="familiesCarousel" class="carousel slide" style="height: 120px;">
      <div class="carousel-inner">
        @foreach($categoriesChunks as $index => $chunk)
          @php
            $isChunkActive = false;
            foreach($chunk as $cat) {
              if (request()->is($cat['url'])) {
                $isChunkActive = true;
                break;
              }
            }
          @endphp
          <div class="carousel-item {{ $isChunkActive ? 'active' : ($index === 0 ? 'active' : '') }}">
            <div class="d-flex justify-content-center flex-wrap gap-3">
              @foreach($chunk as $cat)
                @php
                  $isActive = request()->is(trim($cat['url'], '/') . '*');
                @endphp

                <a href="{{ $cat['url'] }}" class="text-decoration-none" style="width: 120px;">
                  <div class="rounded shadow-sm overflow-hidden position-relative border {{ $isActive ? 'border-success border-4' : 'border-0' }}" style="aspect-ratio: 1 / 1;">
                    <img src="{{ $cat['img'] }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $cat['label'] }}">
                    <div class="position-absolute top-50 start-50 translate-middle text-white fw-bold small text-center bg-dark bg-opacity-50 w-100 py-1">
                      {{ $cat['label'] }} ({{ $cat['count'] }}
                    </div>
                  </div>
                </a>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
      @if($categoriesChunks->count() > 1)
        <button class="carousel-control-prev" type="button" data-bs-target="#familiesCarousel" data-bs-slide="prev" style="background-color: rgba(0,0,0,0.7); border: 2px solid white; width: 40px; height: 40px; top: 50%; transform: translateY(-50%); z-index: 10;">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Précédent</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#familiesCarousel" data-bs-slide="next" style="background-color: rgba(0,0,0,0.7); border: 2px solid white; width: 40px; height: 40px; top: 50%; transform: translateY(-50%); z-index: 10;">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Suivant</span>
        </button>
      @endif
    </div>
  </div>
</div>
