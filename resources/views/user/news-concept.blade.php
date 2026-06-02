@extends('user.layouts.app')

@section('title', 'News Concept – Sora Studio')

@section('content')
@include('user.layouts.partials.page-hero', [
  'eyebrow' => 'News Concept',
  'title' => 'Concept mới, set décor và ánh sáng tại Sora',
  'subtitle' => 'Cập nhật phim trường, moodboard và shoot test để bạn hình dung rõ phong cách trước ngày chụp.',
])

<section>
  <div class="container">
    <div class="card-grid">
      <article class="studio-card sr">
        <div class="studio-card-img"><img src="{{ asset('/user/demo.jpg') }}?t=1" alt="Concept Rooftop"></div>
        <div class="studio-card-body">
          <h4>Rooftop · Golden Hour 2026</h4>
          <p>Set ánh hoàng hôn, voan bay nhẹ và tone ấm vintage – phù hợp váy cúp ngực và suit xám xanh.</p>
        </div>
      </article>
      <article class="studio-card sr d2">
        <div class="studio-card-img"><img src="{{ asset('/user/demo.jpg') }}?t=420x250/ecdacf/b07d62?text=Villa+Colonial" alt="Concept Villa"></div>
        <div class="studio-card-body">
          <h4>Villa · Colonial Romance</h4>
          <p>Phông cổ điển Pháp, cầu thang gỗ và cửa sổ kính lớn; ideal cho áo dài và suit ba piece.</p>
        </div>
      </article>
      <article class="studio-card sr d3">
        <div class="studio-card-img"><img src="{{ asset('/user/demo.jpg') }}?t=420x250/e8ddd4/b07d62?text=Minimal+Film" alt="Concept Minimal"></div>
        <div class="studio-card-body">
          <h4>Minimal · Film tone</h4>
          <p>Nền trung tính, ánh sáng mềm; tập trung vào cảm xúc và chân dung đôi – phù hợp editorial.</p>
        </div>
      </article>
    </div>
    <div class="view-all sr d4" style="margin-top:48px">
      <a href="{{ route('user.trang-phuc') }}" class="va-link">Xem trang phục đề xuất theo concept →</a>
    </div>
  </div>
</section>
@endsection
