@extends('user.layouts.app')

@section('title', 'Khách hàng – Sora Studio')

@section('content')
@include('user.layouts.partials.page-hero', [
  'eyebrow' => 'Khách hàng',
  'title' => 'Khoảnh khắc thật từ các cặp đôi',
  'subtitle' => 'Một phần album fine art và pre-wedding đã được cặp đôi chia sẻ công khai. Mỗi bộ ảnh là một câu chuyện riêng.',
])

<section>
  <div class="container">
    <div class="client-gallery">
      <a href="#" class="sr"><img src="{{ asset('/user/demo.jpg') }}?t=400x500/f3e7df/b07d62?text=Album+1" alt="Album khách hàng 1"></a>
      <a href="#" class="sr d2"><img src="{{ asset('/user/demo.jpg') }}?t=400x500/ecdacf/b07d62?text=Album+2" alt="Album khách hàng 2"></a>
      <a href="#" class="sr d3"><img src="{{ asset('/user/demo.jpg') }}?t=400x500/e8ddd4/b07d62?text=Album+3" alt="Album khách hàng 3"></a>
      <a href="#" class="sr"><img src="{{ asset('/user/demo.jpg') }}?t=400x500/f0e6dc/b07d62?text=Album+4" alt="Album khách hàng 4"></a>
      <a href="#" class="sr d2"><img src="{{ asset('/user/demo.jpg') }}?t=400x500/edddd2/b07d62?text=Album+5" alt="Album khách hàng 5"></a>
      <a href="#" class="sr d3"><img src="{{ asset('/user/demo.jpg') }}?t=400x500/f5ebe3/b07d62?text=Album+6" alt="Album khách hàng 6"></a>
    </div>
    <div class="view-all sr d4" style="margin-top:48px">
      <a href="{{ route('user.blog-cuoi') }}" class="va-link">Đọc blog cưới &amp; kinh nghiệm chụp →</a>
    </div>
  </div>
</section>
@endsection
