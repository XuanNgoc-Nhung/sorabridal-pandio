@extends('user.layouts.app')

@section('title', 'Trang phục – Sora Studio')

@section('content')
@include('user.layouts.partials.page-hero', [
  'eyebrow' => 'Trang phục',
  'title' => 'Váy cưới, vest & áo dài tại studio',
  'subtitle' => 'Kho thiết kế in-house và đối tác designer: chọn trước buổi fitting, phối theo concept và phim trường bạn đã chọn.',
])

<section class="bg-warm">
  <div class="container">
    <div class="card-grid">
      <article class="studio-card sr">
        <div class="studio-card-img"><img src="{{ asset('/user/demo.jpg') }}?t=420x280/f5e8e0/b07d62?text=Váy+cưới" alt="Váy cưới"></div>
        <div class="studio-card-body">
          <h4>Váy cưới</h4>
          <p>Silhouette A-line, mermaid, minimal satin; hỗ trợ chỉnh size theo fitting 1–2 tuần trước chụp.</p>
        </div>
      </article>
      <article class="studio-card sr d2">
        <div class="studio-card-img"><img src="{{ asset('/user/demo.jpg') }}?t=420x280/ede5de/b07d62?text=Vest+%26+Suit" alt="Vest"></div>
        <div class="studio-card-body">
          <h4>Vest &amp; suit</h4>
          <p>Tone navy, charcoal, beige; mix layer áo gile và phụ kiện pocket square theo concept.</p>
        </div>
      </article>
      <article class="studio-card sr d3">
        <div class="studio-card-img"><img src="{{ asset('/user/demo.jpg') }}?t=420x280/f0e6dc/b07d62?text=Áo+dài" alt="Áo dài"></div>
        <div class="studio-card-body">
          <h4>Áo dài cưới</h4>
          <p>Renaissance, gấm, ren hiện đại; phối khăn đóng và trâm cài theo set Villa hoặc rooftop.</p>
        </div>
      </article>
    </div>
    <div class="view-all sr d4" style="margin-top:48px;text-align:center">
      <a href="{{ route('user.dat-lich') }}" class="btn btn-solid">Đặt lịch fitting</a>
    </div>
  </div>
</section>
@endsection
