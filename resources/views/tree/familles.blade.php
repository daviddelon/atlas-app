@php
// Categories creee dynamiquement depuis le controller
  $categoriesChunks = collect($categories)->chunk(7);
@endphp

<div class="container-fluid bg-light py-3 mb-4">
  <style>
    .family-card {
      transition: transform 0.3s ease;
    }
    .family-card-active {
      transform: scale(1.1);
      z-index: 10;
    }
  </style>
  <div class="container">
    <div id="familiesCarousel" class="carousel slide" style="height: 120px;" data-bs-interval="false">
      <div class="carousel-inner">
        @php
          $activeSlideIndex = -1;
          foreach($categoriesChunks as $chunkIndex => $chunk) {
            foreach($chunk as $cat) {
              if (request()->path() === trim($cat['url'], '/')) {
                $activeSlideIndex = $chunkIndex;
                break 2;
              }
            }
          }
          // Si aucune slide ne correspond, utiliser la première
          if ($activeSlideIndex === -1) {
            $activeSlideIndex = 0;
          }
        @endphp

        @foreach($categoriesChunks as $index => $chunk)
          <div class="carousel-item {{ $index === $activeSlideIndex ? 'active' : '' }}">
            <div class="d-flex justify-content-center flex-wrap gap-3">
              @foreach($chunk as $cat)
                @php
                  $isActive = request()->is(trim($cat['url'], '/') . '*');
                @endphp

                <a href="{{ $cat['url'] }}" class="text-decoration-none" style="width: 120px;">
                  <div class="rounded overflow-hidden position-relative {{ $isActive ? 'border border-danger border-4 shadow-lg' : 'border-0 shadow-sm' }}" style="aspect-ratio: 1 / 1;">
                    <img src="{{ $cat['img'] }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $cat['label'] }}">
                    <div class="position-absolute top-50 start-50 translate-middle text-white fw-bold small text-center bg-dark bg-opacity-50 w-100 py-1">
                      {{ $cat['label'] }} ({{ $cat['count'] }})
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
