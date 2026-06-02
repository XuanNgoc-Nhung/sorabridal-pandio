@extends('user.layouts.app')

@section('title', 'Dịch vụ – Sora Studio')

@section('content')
@include('user.layouts.partials.page-hero', [
  'eyebrow' => 'Dịch vụ',
  'title' => 'Gói chụp ảnh cưới & trải nghiệm trọn gói',
  'subtitle' => 'Từ pre-wedding đến album hoàn chỉnh: concept, trang phục, make-up và hậu kỳ fine art trong một hành trình đồng hành rõ ràng.',
])

<section class="bg-warm">
  <div class="container">
    <p class="page-lead sr d2">Sora Studio thiết kế gói dịch vụ linh hoạt theo thời gian chụp, số concept và phim trường. Mọi gói đều có stylist, make-up chuyên nghiệp và chỉnh màu theo phong cách fine art đặc trưng của studio.</p>
    <div class="price-grid">
      <div class="price-card sr">
        <div class="price-name">Gói Dấu yêu</div>
        <div class="price-amount"><span class="count-up" data-count="6.3" data-decimals="1">0</span><small> triệu</small></div>
        <ul class="price-features">
          <li>Chụp 4 giờ tại studio</li>
          <li>1 concept – 1 trang phục</li>
          <li>25 ảnh chỉnh sửa tinh tế</li>
          <li>Ảnh liền tay ngay ngày chụp</li>
        </ul>
        <a href="{{ route('user.dat-lich') }}" class="btn btn-outline" style="width:100%;justify-content:center">Tư vấn gói này</a>
      </div>
      <div class="price-card featured sr d2">
        <div class="price-name">Gói Cảm xúc</div>
        <div class="price-amount"><span class="count-up" data-count="11.9" data-decimals="1">0</span><small> triệu</small></div>
        <ul class="price-features">
          <li>Chụp 7 giờ tại studio</li>
          <li>2 concept – 2 trang phục</li>
          <li>45 ảnh chỉnh sửa cao cấp</li>
          <li>Album in thiết kế cao cấp</li>
          <li>Video highlight 60 giây</li>
        </ul>
        <a href="{{ route('user.dat-lich') }}" class="btn btn-solid" style="width:100%;justify-content:center">Tư vấn gói này</a>
      </div>
      <div class="price-card sr d3">
        <div class="price-name">Gói Trọn vẹn</div>
        <div class="price-amount"><span class="count-up" data-count="16.8" data-decimals="1">0</span><small> triệu</small></div>
        <ul class="price-features">
          <li>Chụp 2 ngày liên tiếp</li>
          <li>Phim trường riêng (Rooftop + Villa)</li>
          <li>Váy cưới designer cao cấp</li>
          <li>70 ảnh chỉnh sửa điện ảnh</li>
          <li>Video highlight 3 phút full</li>
        </ul>
        <a href="{{ route('user.dat-lich') }}" class="btn btn-outline" style="width:100%;justify-content:center">Tư vấn gói này</a>
      </div>
    </div>
    <div class="view-all sr d4">
      <a href="{{ route('user.home') }}" class="va-link">← Về trang chủ</a>
    </div>
  </div>
</section>
@endsection
