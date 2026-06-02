@extends('user.layouts.app')

@section('title', 'Blog cưới – Sora Studio')

@section('content')
@include('user.layouts.partials.page-hero', [
  'eyebrow' => 'Blog cưới',
  'title' => 'Chuẩn bị ngày cưới &amp; buổi chụp',
  'subtitle' => 'Checklist timeline, chọn concept, skincare và tips tạo dáng tự nhiên trước ống kính.',
])

<section class="bg-warm">
  <div class="container">
    <div class="blog-full-grid">
      <article class="blog-card sr">
        <div class="blog-img"><img src="{{ asset('/user/demo.jpg') }}?t=480x230/f3e7df/b07d62?text=Timeline+6+tháng" alt=""></div>
        <div class="blog-body">
          <span class="blog-cat">Chuẩn bị</span>
          <h4>Timeline 6 tháng trước ngày chụp</h4>
          <p>Fitting váy, thử make-up và book phim trường – thứ tự hợp lý để không bị dồn việc sát ngày.</p>
          <span class="read-more">Đọc tiếp <i class="fas fa-arrow-right"></i></span>
        </div>
      </article>
      <article class="blog-card sr d2">
        <div class="blog-img"><img src="{{ asset('/user/demo.jpg') }}?t=480x230/ecdacf/b07d62?text=Golden+Hour" alt=""></div>
        <div class="blog-body">
          <span class="blog-cat">Ánh sáng</span>
          <h4>Tận dụng golden hour tại Rooftop</h4>
          <p>Khung giờ ưu tiên và phối màu trang phục để da và nền trời hài hòa trong ảnh film tone.</p>
          <span class="read-more">Đọc tiếp <i class="fas fa-arrow-right"></i></span>
        </div>
      </article>
      <article class="blog-card sr d3">
        <div class="blog-img"><img src="{{ asset('/user/demo.jpg') }}?t=480x230/e8ddd4/b07d62?text=Concept+Villa" alt=""></div>
        <div class="blog-body">
          <span class="blog-cat">Concept</span>
          <h4>Colonial Villa: props và tạo dáng</h4>
          <p>Tư thế đi cầu thang, sofa và cửa sổ – gợi ý tự nhiên cho cặp đôi ít kinh nghiệm chụp ảnh.</p>
          <span class="read-more">Đọc tiếp <i class="fas fa-arrow-right"></i></span>
        </div>
      </article>
      <article class="blog-card sr">
        <div class="blog-img"><img src="{{ asset('/user/demo.jpg') }}?t=480x230/f0e6dc/b07d62?text=Skincare" alt=""></div>
        <div class="blog-body">
          <span class="blog-cat">Beauty</span>
          <h4>Skincare tối giản trước ngày chụp</h4>
          <p>Tránh peel mới, giữ ẩm và ngủ đủ giấc để lớp nền make-up bám mịn suốt nhiều giờ.</p>
          <span class="read-more">Đọc tiếp <i class="fas fa-arrow-right"></i></span>
        </div>
      </article>
      <article class="blog-card sr d2">
        <div class="blog-img"><img src="{{ asset('/user/demo.jpg') }}?t=480x230/edddd2/b07d62?text=Album+in" alt=""></div>
        <div class="blog-body">
          <span class="blog-cat">Sau chụp</span>
          <h4>Chọn ảnh in album: bao nhiêu là đủ?</h4>
          <p>Cân bằng giữa full body, close-up và chi tiết – tránh trùng lặp khiến album dài nhưng thiếu nhịp.</p>
          <span class="read-more">Đọc tiếp <i class="fas fa-arrow-right"></i></span>
        </div>
      </article>
      <article class="blog-card sr d3">
        <div class="blog-img"><img src="{{ asset('/user/demo.jpg') }}?t=480x230/f5ebe3/b07d62?text=Ngày+cưới" alt=""></div>
        <div class="blog-body">
          <span class="blog-cat">Wedding day</span>
          <h4>Phối hợp ekip studio trong ngày trọng đại</h4>
          <p>Timeline sáng–chiều, điểm chạm với photographer và coordinator để không lỡ khoảnh khắc quan trọng.</p>
          <span class="read-more">Đọc tiếp <i class="fas fa-arrow-right"></i></span>
        </div>
      </article>
    </div>
    <div class="view-all sr d4" style="margin-top:44px">
      <a href="{{ route('user.news-concept') }}" class="va-link">← Xem News Concept</a>
    </div>
  </div>
</section>
@endsection
