@extends('user.layouts.app')

@section('title', 'Sora Studio – Studio Ảnh Cưới Nghệ Thuật Hà Nội')

@section('body_class', 'page-home')

@push('styles')
<link rel="stylesheet" href="{{ asset('user/css/home-index-mobile.css') }}">
@endpush

@section('content')
@php
    $homeBanners = [
        [
            'image' => asset('user/matTien.webp'),
            'alt' => 'Mặt tiền Sora Studio Hà Nội',
            'eyebrow' => 'Sora Studio · Hà Nội · Est. 2022',
            'title' => 'Studio ảnh cưới nghệ thuật tại Tây Hồ',
            'desc' => 'Không gian chụp fine art phong cách Hàn Quốc, phim trường độc quyền và dịch vụ trọn gói từ pre-wedding đến ngày cưới.',
            'cta' => 'Nhận tư vấn ngay',
            'url' => route('user.dat-lich'),
        ],
        [
            'image' => asset('user/phimTruong1.webp'),
            'alt' => 'Phim trường Sora Rooftop',
            'eyebrow' => 'Phim trường độc quyền',
            'title' => 'Sora Rooftop & Biệt thự Sora',
            'desc' => 'Hệ thống phim trường 2000m², sức chứa lên tới 30 cặp đôi cùng lúc — concept sân thượng và hoàng hôn lãng mạn.',
            'cta' => 'Khám phá concept',
            'url' => 'https://album.sorawedding.vn/concept/',
        ],
        [
            'image' => asset('user/anhTrangChu.webp'),
            'alt' => 'Ảnh cưới Sora Bridal',
            'eyebrow' => 'Sora Bridal',
            'title' => 'Ghi dấu khoảnh khắc hạnh phúc',
            'desc' => 'Hơn 5000 cặp đôi đã tin tưởng. Tận tâm – Trách nhiệm – Phát triển trong từng khung hình và từng chi tiết dịch vụ.',
            'cta' => 'Đặt lịch chụp',
            'url' => route('user.dat-lich'),
        ],
    ];
@endphp
<div class="page-home">
<!-- ══════════════════════ BANNER SLIDER ═══════════════════ -->
<section class="home-banner" aria-label="Banner studio">
    <div class="home-banner-slider" id="homeBannerSlider">
        <div class="home-banner-track" id="homeBannerTrack">
            @foreach ($homeBanners as $banner)
            <article class="home-banner-slide{{ $loop->first ? ' is-active' : '' }}">
                <div class="home-banner-media">
                    <img src="{{ $banner['image'] }}" alt="{{ $banner['alt'] }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                </div>
                <div class="home-banner-overlay" aria-hidden="true"></div>
                <div class="home-banner-content container">
                    <span class="home-banner-part home-banner-eyebrow" style="--banner-i:0">{{ $banner['eyebrow'] }}</span>
                    <h2 class="home-banner-part home-banner-title" style="--banner-i:1">{{ $banner['title'] }}</h2>
                    <p class="home-banner-part home-banner-desc" style="--banner-i:2">{{ $banner['desc'] }}</p>
                    {{-- <a href="{{ $banner['url'] }}" class="home-banner-part btn btn-solid home-banner-cta" style="--banner-i:3"
                        @if (str_starts_with($banner['url'], 'http')) target="_blank" rel="noopener noreferrer" @endif>
                        {{ $banner['cta'] }} <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a> --}}
                </div>
            </article>
            @endforeach
        </div>
        <button type="button" class="home-banner-nav prev" id="homeBannerPrev" aria-label="Banner trước">
            <i class="fas fa-chevron-left" aria-hidden="true"></i>
        </button>
        <button type="button" class="home-banner-nav next" id="homeBannerNext" aria-label="Banner sau">
            <i class="fas fa-chevron-right" aria-hidden="true"></i>
        </button>
        <div class="home-banner-dots" id="homeBannerDots" role="tablist" aria-label="Chọn banner"></div>
    </div>
</section>

<!-- ══════════════════════ HERO ══════════════════════════ -->
<section class="hero">
    <div class="container">
        <div class="hero-grid">

            <div class="hero-content">
                <div class="hero-eyebrow sr">
                    <div class="hero-eyebrow-line"></div>
                    <span>Sora Studio · Hà Nội · Est. 2022</span>
                </div>
                <p class="hero-typed-line sr d1">
                    <span class="typing-text" data-typing-speed="36"
                        data-typing-text="Phong cách Hàn Quốc, tinh tế, tối giản"></span><span class="typing-cursor"
                        aria-hidden="true"></span>
                </p>
                <h1 class="hero-title sr d2">
                    Mỗi chiếc váy cưới là <br>
                    <em>một giấc mơ.</em><br> Mỗi tấm hình là<br> một chuyện tình đẹp.
                </h1>
                <p class="hero-desc sr d3">Theo đuổi phong cách Hàn Quốc tinh tế và nhẹ nhàng, chúng tôi ghi dấu ấn qua
                    những concept sân thượng độc quyền cùng khoảnh khắc hoàng hôn lãng mạn. Mỗi khung hình tại Sora đều
                    mang một dấu ấn riêng, nơi những rung động chân thật nhất được thăng hoa. Với sự tận tâm trong từng
                    chi tiết và dịch vụ chăm sóc chuẩn mực, Sora Bridal cam kết đồng hành cùng bạn kiến tạo nên những kỷ
                    niệm cưới hoàn mỹ và dạt dào cảm xúc.</p>
                <div class="hero-actions sr d4">
                    <a href="{{ route('user.dat-lich') }}" class="btn btn-solid">Nhận tư vấn ngay <i
                            class="fas fa-arrow-right"></i></a>
                    {{-- <a href="#" class="btn btn-outline">Xem bộ sưu tập</a> --}}
                </div>
                <div class="hero-divider sr d5"></div>
                <div class="hero-stats sr d5">
                    <div class="stat-item">
                        <span class="stat-num count-up" data-count="5" data-suffix="+">0</span>
                        <div class="stat-label">Năm kinh nghiệm</div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-num count-up" data-count="5000" data-format="k" data-suffix="+">0</span>
                        <div class="stat-label">Cặp đôi hạnh phúc</div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-num count-up" data-count="2">0</span>
                        <div class="stat-label">Phim trường độc quyền</div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-num count-up" data-count="200" data-suffix="+">0</span>
                        <div class="stat-label">Concept</div>
                    </div>
                </div>
            </div>

            <div class="hero-img-wrap sr sr-zoom d2">
                <div class="hero-img-main">
                    <img src="{{ asset('/user/anhTrangChu.webp') }}"
                        style="width: 100%; height: 100%; object-fit: cover;" alt="Sora Studio wedding photography">
                </div>
                <div class="hero-float-img">
                    <img src="{{ asset('/user/matTien.webp') }}"
                        style="width: 100%; height: 100%; object-fit: fill !important;" alt="Studio detail">
                </div>
                {{-- <div class="hero-badge">
          <span class="badge-icon">🏆</span>
          <div>
            <span class="badge-strong">Top Studio 2025</span>
            <span class="badge-sub">Hanoi Wedding Awards</span>
          </div>
        </div> --}}
            </div>

        </div>
    </div>
</section>

<!-- ══════════════════════ ABOUT ═════════════════════════ -->
<section>
    <div class="container">
        <div class="about-grid">

            <div class="about-img-wrap sr sr-left sr-blur-in">
                <div class="about-img-main">
                    <img src="{{ asset('/user/anhFouder.webp') }}" style="width: 100%; height: 100%; object-fit: cover;"
                        alt="Founder Sora Studio">
                </div>
                {{-- <div class="about-img-accent">
          <img src="{{ asset('/user/demo.jpg') }}?t=190x190/ecdacf/b07d62?text=Award" alt="Award">
            </div> --}}
        </div>

        <div class="about-content sr sr-right d1 sr-flip">
            <span class="sec-tag">Lời nhắn đầu tiên</span>
            <h2 class="sec-title" style="margin-bottom:18px">Từ trái tim Sora</h2>
            {{-- <div class="about-pills">
                    <span class="pill">Fine Art Photography</span>
                    <span class="pill">10+ Năm kinh nghiệm</span>
                    <span class="pill">Sora Rooftop & Villa</span>
                </div> --}}
            <div class="quote-open">"</div>
            <p class="about-quote">Tại Sora Bridal, chúng tôi không chỉ thực hiện một bộ ảnh hay một lễ cưới, mà
                chúng tôi đặt trọn tâm huyết như đang chăm chút cho ngày trọng đại của chính mình, để mỗi trải
                nghiệm và từng sản phẩm tới tay khách hàng đều là sự kết tinh hoàn hảo nhất dành cho ngày hạnh phúc
                của hai bạn!
                <br>
                Sora Bridal Tận Tâm - Trách Nhiệm - Phát Triển.</p>
            <div class="about-sig">
                <div class="sig-line"></div>
                <div>
                    <div class="sig-name">Đỗ Tuấn Anh</div>
                    <div class="sig-role">Founder Sora Bridal</div>
                </div>
            </div>
        </div>

    </div>
    </div>
</section>

<!-- ══════════════════════ STUDIO / CONCEPT TABS ══════════ -->
<section class="bg-warm">
    <div class="container">
        <div class="reveal-anim section-head" data-animate="fadeInUp" style="text-align:center">
            <span class="sec-tag" style="display:block">Không gian chụp ảnh</span>
            <h2 class="sec-title center">Phim trường &amp; Concept độc quyền</h2>
            <p class="sec-sub center">Loạt hệ thống phim trường với tổng diện tích 2000m2. Sức chứa lên tới 30 cặp đôi
                tại cùng 1 thời điểm</p>
        </div>
        {{-- <div class="sr d1">
            <div class="tabs-pill">
                <button class="tab-btn active" data-tab="tab-studio">📸 Phim trường nổi bật</button>
                <button class="tab-btn" data-tab="tab-concept">🎨 Concept nghệ thuật</button>
            </div>
        </div> --}}

        <div id="tab-studio" class="tab-pane active">
            <div class="card-grid">
                <div class="studio-card sr d2 sr-blur-in"
                    onclick="window.location.href='https://album.sorawedding.vn/concept/'">
                    <div class="studio-card-img"><img src="{{ asset('/user/phimTruong1.webp') }}"
                            alt="Ánh sáng tự nhiên"></div>
                    <div class="studio-card-body">
                        <h4>Sora’s rooftop – tầng 8, 315 bạch mai, Hà Nội</h4>
                        <p>Tầm nhìn panoramic, golden hour, ánh sáng tự nhiên</p>
                    </div>
                </div>
                <div class="studio-card sr d2" onclick="window.location.href='https://album.sorawedding.vn/concept/'">
                    <div class="studio-card-img"><img src="{{ asset('/user/phimTruong2.webp') }}" alt="Biệt Thự Sora">
                    </div>
                    <div class="studio-card-body">
                        <h4>Sora’s mansion – Hoàng mai, Hà Nội</h4>
                        <p>Concept sân vườn, minimalist, không gian giàu cảm xúc</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="tab-concept" class="tab-pane">
            <div class="card-grid">
                <div class="studio-card sr">
                    <div class="studio-card-img"><img
                            src="{{ asset('/user/demo.jpg') }}?t=420x250/f2e2d6/b07d62?text=Fine+Art" alt="Fine Art">
                    </div>
                    <div class="studio-card-body">
                        <h4>Fine Art Cinematic</h4>
                        <p>Điêu khắc ánh sáng, đậm chất hội họa điện ảnh</p>
                    </div>
                </div>
                <div class="studio-card sr d2">
                    <div class="studio-card-img"><img
                            src="{{ asset('/user/demo.jpg') }}?t=420x250/edd9cf/b07d62?text=Boho" alt="Boho"></div>
                    <div class="studio-card-body">
                        <h4>Boho phóng khoáng</h4>
                        <p>Hoa lá, cỏ cây, tự do bay bổng giữa thiên nhiên</p>
                    </div>
                </div>
                <div class="studio-card sr d3">
                    <div class="studio-card-img"><img
                            src="{{ asset('/user/demo.jpg') }}?t=420x250/f7eae3/b07d62?text=Á+Đông" alt="Á Đông"></div>
                    <div class="studio-card-body">
                        <h4>Cổ điển Á Đông</h4>
                        <p>Áo dài, nón lá, hoa sen – vẻ đẹp thuần Việt tinh tế</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════ COLLECTION SLIDER ════════════ -->
<section class="collection-section">
    <div class="container">
        <div class="watermark sr sr-rise d1">Sora Bridal</div>
        <div class="reveal-anim section-head" data-animate="fadeInDown" style="text-align:center">
            <span class="sec-tag" style="display:block">Những thiết kế đẳng cấp</span>
            <h2 class="sec-title center">Bộ sưu tập váy cưới đa dạng</h2>
            <p class="sec-sub center">Hàng trăm mẫu váy cưới được cập nhật liên tục, theo xu hướng. đáp ứng được đầy đủ
                phong cách và cá nhân hóa theo từng cô dâu</p>
        </div>

        <div class="sr d2">
            <div class="outer-track-wrap">
                <div class="outer-track" id="outerTrack">

                    <!-- Collection 1 -->
                    <div class="col-slide">
                        <div class="col-layout">
                            <div class="col-info">
                                <div class="col-num sec-tag">01</div>
                                <h3 class="col-title sec-tag text-pink">Dòng váy VIP</h3>
                                <p class="col-desc">Những thiết kế váy cưới hiện đại, thanh lịch và dễ mặc. Dòng VIP
                                    được SORA BRIDAL chọn lọc kỹ lưỡng để bạn tự tin tỏa sáng trong ngày cưới mà không
                                    cần đắn đo về chi phí.</p>
                                <div class="col-actions">
                                    <a href="https://album.sorawedding.vn/soravaycuoi/vayluxury1/" target="_blank"
                                        class="btn btn-solid" style="font-size:0.84rem;padding:11px 24px">Xem
                                        BST váy <i class="fas fa-arrow-right"></i></a>
                                    {{-- <a href="#" class="link-arr">Tất cả váy <i class="fas fa-arrow-right"></i></a> --}}
                                </div>
                            </div>
                            <div>
                                <div class="inner-wrap" id="iw-0">
                                    <div class="inner-track" id="it-0">
                                        <div class="in-slide"><img src="{{ asset('/user/vayVip1.jpg') }}"
                                                style="width: 100%; height: 100%; object-fit: cover;" alt="S1"></div>
                                        <div class="in-slide"><img src="{{ asset('/user/vayVip2.jpg') }}"
                                                style="width: 100%; height: 100%; object-fit: cover;" alt="S2"></div>
                                        <div class="in-slide"><img src="{{ asset('/user/vayVip3.jpg') }}"
                                                style="width: 100%; height: 100%; object-fit: cover;" alt="S3"></div>
                                        {{-- <div class="in-slide"><img src="{{ asset('/user/demo.jpg') }}?t=380x430/f5e8e0/b07d62?text=Shanghai+4"
                                        alt="S4">
                                    </div> --}}
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collection 2 -->
                <div class="col-slide">
                    <div class="col-layout">
                        <div class="col-info">
                            <div class="col-num sec-tag">02</div>
                            {{-- <span class="sec-tag">BST Cao cấp</span> --}}
                            <h3 class="col-title sec-tag text-pink">Váy luxury</h3>
                            <p class="col-desc">Những thiết kế cao cấp với chất liệu sang trọng và đường may tinh xảo.
                                Dòng Luxury không chỉ đẹp trên ảnh mà còn hoàn hảo để bạn mặc trong ngày trọng đại</p>
                            <div class="col-actions">
                                <a href="https://album.sorawedding.vn/soravaycuoi/radiancecouture/" target="_blank"
                                    class="btn btn-solid" style="font-size:0.84rem;padding:11px 24px">Xem
                                    BST váy<i class="fas fa-arrow-right"></i></a>
                                {{-- <a href="#" class="link-arr">Tất cả váy <i class="fas fa-arrow-right"></i></a> --}}
                            </div>
                        </div>
                        <div>
                            <div class="inner-wrap" id="iw-1">
                                <div class="inner-track" id="it-1">
                                    <div class="in-slide"><img src="{{ asset('/user/vayLux1.jpg') }}"
                                            style="width: 100%; height: 100%; object-fit: cover;" alt="R1"></div>
                                    <div class="in-slide"><img src="{{ asset('/user/vayLux2.jpg') }}"
                                            style="width: 100%; height: 100%; object-fit: cover;" alt="R2"></div>
                                    <div class="in-slide"><img src="{{ asset('/user/vayLux3.jpg') }}"
                                            style="width: 100%; height: 100%; object-fit: cover;" alt="R3"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collection 3 -->
                <div class="col-slide">
                    <div class="col-layout">
                        <div class="col-info">
                            <div class="col-num sec-tag">03</div>
                            {{-- <span class="sec-tag">BST Lãng mạn</span> --}}
                            <h3 class="col-title sec-tag text-pink">Váy diamond</h3>
                            <p class="col-desc">Dòng váy cao cấp nhất của SORA BRIDAL. Mỗi chiếc váy Diamond được thiết
                                kế và may đo riêng theo số đo, phong cách và “ước mơ” của bạn. Không có chiếc váy nào
                                giống chiếc váy nào – vì mỗi chiếc đều là độc bản.</p>
                            <div class="col-actions">
                                <a href="https://album.sorawedding.vn/soravaycuoi/symphonycouture/" target="_blank"
                                    class="btn btn-solid" style="font-size:0.84rem;padding:11px 24px">Xem
                                    BST váy <i class="fas fa-arrow-right"></i></a>
                                {{-- <a href="#" class="link-arr">Tất cả váy <i class="fas fa-arrow-right"></i></a> --}}
                            </div>
                        </div>
                        <div>
                            <div class="inner-wrap" id="iw-2">
                                <div class="inner-track" id="it-2">
                                    <div class="in-slide"><img src="{{ asset('/user/vaySignature1.jpg') }}"
                                            style="width: 100%; height: 100%; object-fit: cover;" alt="Sy1"></div>
                                    <div class="in-slide"><img src="{{ asset('/user/vaySignature2.jpg') }}"
                                            style="width: 100%; height: 100%; object-fit: cover;" alt="Sy2"></div>
                                    <div class="in-slide"><img src="{{ asset('/user/vaySignature3.jpg') }}"
                                            style="width: 100%; height: 100%; object-fit: cover;" alt="Sy3"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collection 4 -->
                {{-- <div class="col-slide">
            <div class="col-layout">
              <div class="col-info">
                <div class="col-num">04</div>
                <span class="sec-tag">BST Công chúa</span>
                <h3 class="col-title">Rose Princess</h3>
                <p class="col-desc">Lấy cảm hứng từ những nàng công chúa kiêu sa. Thiết kế xòe bồng, đính kết hoa 3D và ngọc trai tinh xảo, mang đến vẻ đẹp ngọt ngào và lãng mạn.</p>
                <div class="col-actions">
                  <a href="#" class="btn btn-solid" style="font-size:0.84rem;padding:11px 24px">Xem BST <i class="fas fa-arrow-right"></i></a>
                  <a href="#" class="link-arr">Tất cả váy <i class="fas fa-arrow-right"></i></a>
                </div>
              </div>
              <div>
                <div class="inner-wrap" id="iw-3">
                  <div class="inner-track" id="it-3">
                    <div class="in-slide"><img src="{{ asset('/user/demo.jpg') }}?t=380x430/f9efe8/b07d62?text=Rose+1"
                alt="Ro1">
            </div>
            <div class="in-slide"><img src="{{ asset('/user/demo.jpg') }}?t=380x430/f6e9e2/b07d62?text=Rose+2"
                    alt="Ro2"></div>
            <div class="in-slide"><img src="{{ asset('/user/demo.jpg') }}?t=380x430/f3e3db/b07d62?text=Rose+3"
                    alt="Ro3"></div>
            <div class="in-slide"><img src="{{ asset('/user/demo.jpg') }}?t=380x430/f0ddd4/b07d62?text=Rose+4"
                    alt="Ro4"></div>
        </div>

    </div>
    </div>
    </div>
    </div> --}}

    </div><!-- /outer-track -->
    </div>

    <div class="outer-nav">
        <div class="outer-dots" id="outerDots"></div>
        <div class="outer-btns">
            <button class="outer-btn" id="outerPrev">❮</button>
            <button class="outer-btn" id="outerNext">❯</button>
        </div>
    </div>
    </div><!-- /sr -->
</section>

<!-- ══════════════════════ BEFORE / AFTER ════════════════ -->
<section class="ba-section">
    <div class="container">
        <div class="ba-header reveal-anim reveal-once section-head" data-animate="zoomIn">
            <span class="sec-tag" style="display:block">Trước &amp; Sau chỉnh sửa</span>
            <h2 class="sec-title center">Hậu kì đến khi ưng ý</h2>
        </div>
        <div class="ba-intro sr sr-once d1">
            <p>Không chỉ chụp ảnh – Sora Studio đưa mỗi khoảnh khắc lên tầm cao nghệ thuật. cùng Xem sự khác biệt kỳ
                diệu sau khi qua tay đội ngũ retouch chuyên nghiệp của sora dưới đây nhé...</p>
        </div>

        <div class="ba-grid">

            <!-- Slider 1 -->
            <div class="sr sr-once">
                <div class="ba-card-label">1. Ảnh photoshop mặt cỏ trước & sau</div>
                <div class="ba-slider-wrap" id="ba1">
                    <div class="ba-img before">
                        <img src="{{ asset('/user/anhPtsTruoc1.webp') }}"
                            style="width: 100%; height: 100%; object-fit: cover;" alt="Trước retouch">
                    </div>
                    <div class="ba-img after" id="ba1-after">
                        <img src="{{ asset('/user/anhPtsSau1.webp') }}"
                            style="width: 100%; height: 100%; object-fit: cover;" alt="Sau retouch">
                    </div>
                    <div class="ba-handle" id="ba1-handle">
                        <div class="ba-handle-circle">
                            <i class="fas fa-chevron-left"></i><i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                    <span class="ba-label-before">Trước</span>
                    <span class="ba-label-after">Sau</span>
                </div>
            </div>

            <!-- Slider 2 -->
            <div class="sr sr-once d1">
                <div class="ba-card-label">2. Ảnh photoshop giả nắng trước & sau</div>
                <div class="ba-slider-wrap" id="ba2">
                    <div class="ba-img before">
                        <img src="{{ asset('/user/anhPtsTruoc2.webp') }}"
                            style="width: 100%; height: 100%; object-fit: cover;" alt="Trước retouch">
                    </div>
                    <div class="ba-img after" id="ba2-after">
                        <img src="{{ asset('/user/anhPtsSau2.webp') }}"
                            style="width: 100%; height: 100%; object-fit: cover;" alt="Sau retouch">
                    </div>
                    <div class="ba-handle" id="ba2-handle">
                        <div class="ba-handle-circle">
                            <i class="fas fa-chevron-left"></i><i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                    <span class="ba-label-before">Trước</span>
                    <span class="ba-label-after">Sau</span>
                </div>
            </div>

            <!-- Slider 3 -->
            {{-- <div class="sr sr-once">
        <div class="ba-card-label">💎 Váy đính đá – Chỉnh sáng tôn dáng</div>
        <div class="ba-slider-wrap" id="ba3">
          <div class="ba-img before">
            <img src="{{ asset('/user/demo.jpg') }}?t=860x500/bea898/6f4535?text=Trước+-+Nền+tối+mờ" alt="Trước
            retouch">
        </div>
        <div class="ba-img after" id="ba3-after">
            <img src="{{ asset('/user/demo.jpg') }}?t=860x500/f8ede5/b07d62?text=Sau+-+Lung+linh+lấp+lánh"
                alt="Sau retouch">
        </div>
        <div class="ba-handle" id="ba3-handle">
            <div class="ba-handle-circle">
                <i class="fas fa-chevron-left"></i><i class="fas fa-chevron-right"></i>
            </div>
        </div>
        <span class="ba-label-before">Trước</span>
        <span class="ba-label-after">Sau</span>
    </div>
    </div> --}}

    <!-- Slider 4 -->
    {{-- <div class="sr sr-once d1">
        <div class="ba-card-label">🌿 Concept ngoại cảnh – Nâng màu tự nhiên</div>
        <div class="ba-slider-wrap" id="ba4">
          <div class="ba-img before">
            <img src="{{ asset('/user/demo.jpg') }}?t=860x500/c2aa98/7a5540?text=Trước+-+Ánh+sáng+xỉn+màu" alt="Trước
    retouch">
    </div>
    <div class="ba-img after" id="ba4-after">
        <img src="{{ asset('/user/demo.jpg') }}?t=860x500/fdf4ef/b88060?text=Sau+-+Golden+hour+ấm+áp" alt="Sau retouch">
    </div>
    <div class="ba-handle" id="ba4-handle">
        <div class="ba-handle-circle">
            <i class="fas fa-chevron-left"></i><i class="fas fa-chevron-right"></i>
        </div>
    </div>
    <span class="ba-label-before">Trước</span>
    <span class="ba-label-after">Sau</span>
    </div>
    </div> --}}

    </div><!-- /ba-grid -->
    </div>
</section>

<!-- ══════════════════════ VIDEO SECTION ════════════════ -->
<div class="video-section">
    <div class="video-inner">
        <div class="video-bg">
            <img src="{{ asset('/user/demo.jpg') }}?t=1440x620/2a1f17/b07d62?text=Sora+Wedding+Film"
                alt="Video background">
        </div>
        <div class="video-overlay"></div>
        <div class="video-content">
            <span class="video-tag sr sr-fast">Xem thêm · Showreel 2025</span>
            <h2 class="video-title sr d1 sr-flip">Tình yêu được kể<br>bằng hình ảnh điện ảnh</h2>
            <p class="video-sub sr d2">Hơn 3.800 cặp đôi đã tin tưởng Sora Studio lưu giữ khoảnh khắc trọng đại nhất</p>
            <div class="sr d2">
                <button class="play-btn" id="playBtn" aria-label="Xem video">
                    <i class="fas fa-play" style="margin-left:4px"></i>
                </button>
            </div>
            <div class="video-stats sr d3">
                <div>
                    <span class="vs-num count-up" data-count="98" data-format="%" data-suffix="%">0</span>
                    <div class="vs-label">Khách hàng hài lòng</div>
                </div>
                <div>
                    <span class="vs-num count-up" data-count="90" data-format="%" data-suffix="%">0</span>
                    <div class="vs-label">Khách hàng giới thiệu lại</div>
                </div>
                <div>
                    <span class="vs-num count-up" data-count="10" data-suffix="+">0</span>
                    <div class="vs-label">Đối tác đồng hành</div>
                </div>
                <div>
                    <span class="vs-num count-up" data-count="10" data-suffix="M+">0</span>
                    <div class="vs-label">Lượt xem trên các nền tảng MXH</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Video modal -->
<div class="video-modal" id="videoModal">
    <div class="video-modal-inner">
        <button class="modal-close" id="modalClose"><i class="fas fa-times"></i></button>
        <iframe id="videoFrame" src="" allowfullscreen allow="autoplay"></iframe>
    </div>
</div>

<!-- ══════════════════════ PRICING ═══════════════════════ -->
<section class="bg-warm">
    <div class="container">
        <div class="reveal-anim section-head" data-animate="fadeInUp" style="text-align:center">
            <span class="sec-tag" style="display:block">Bảng giá</span>
            <h2 class="sec-title center">Chi phí hợp lý – Giá trị vượt mong đợi</h2>
        </div>
        <div class="price-intro sr d1">
            <i class="fas fa-gem"></i>
            <h3>Tại sao Sora có mức giá tối ưu?</h3>
            <p>Không chỉ sở hữu phim trường độc quyền mà từ khâu may đo váy tới in ấn ảnh cưới. Sora đều có xưởng sản
                xuất riêng giúp tiết kiệm chi phí trung gian. Đảm bảo dâu rể nhận được trải nghiệm dịch vụ đẳng cấp với
                mức giá cạnh tranh nhất, chất lượng ảnh cao cấp, váy cưới khác biệt.</p>
        </div>
        <div class="price-grid">
            <div class="price-card sr">
                <div class="price-name">Gói Dấu yêu</div>
                <div class="price-amount"><span class="count-up" data-count="6.3" data-decimals="1">0</span><small>
                        triệu</small></div>
                <ul class="price-features">
                    <li>4 concept tại studio</li>
                    <li>2 váy – 2 vest cho ngày chụp</li>
                    <li>1 váy ngày cưới dòng VIP</li>
                    <li>30 ảnh chỉnh sửa</li>
                </ul>
                <a href="#" class="btn btn-outline" style="width:100%;justify-content:center">Chọn gói này</a>
            </div>
            <div class="price-card featured sr d2">
                <div class="price-name">Gói Cảm xúc</div>
                <div class="price-amount"><span class="count-up" data-count="11.9" data-decimals="1">0</span><small>
                        triệu</small></div>
                <ul class="price-features">
                    <li>Chụp 2 điêm ngoại cảnh và studio</li>
                    <li>Thêm lựa chon concept hoàng hôn/ tối</li>
                    <li>1 váy cưới dòng luxury nước đầu</li>
                    <li>Album ảnh cưới 25x35</li>
                    <li>40 ảnh chỉnh sửa</li>
                </ul>
                <a href="#" class="btn btn-solid" style="width:100%;justify-content:center">Chọn gói này</a>
            </div>
            <div class="price-card sr d3">
                <div class="price-name">Gói Trọn vẹn</div>
                <div class="price-amount"><span class="count-up" data-count="16.8" data-decimals="1">0</span><small>
                        triệu</small></div>
                <ul class="price-features">
                    <li>3 điểm chụp: Nội thành, phim trường, studio</li>
                    <li>3 váy – 3 vest cho ngày chụp</li>
                    <li>1 váy cưới limited may đo riêng</li>
                    <li>71 máy chụp ảnh phóng sự cưới</li>
                    <li>ảnh in ấn chất lượng MICA HD cao cấp</li>
                </ul>
                <a href="#" class="btn btn-outline" style="width:100%;justify-content:center">Chọn gói này</a>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════ REVIEWS ═══════════════════════ -->
<section>
    <div class="container">
        <div class="reveal-anim section-head" data-animate="fadeInUp" style="text-align:center">
            <span class="sec-tag" style="display:block">Niềm tin</span>
            <h2 class="sec-title center">KOL &amp; Cặp đôi hạnh phúc nói gì?</h2>
            <p class="sec-sub center">Hơn 500+ đánh giá 5 sao trên Google &amp; Facebook</p>
        </div>
        <div class="review-grid">
            <div class="rev-card sr">
                <div class="rev-stars">★★★★★</div>
                <p class="rev-text">"Bộ ảnh cưới của tôi như một thước phim cổ tích. Sora Studio quá xuất sắc – từ
                    concept đến từng chiếc váy đều hoàn hảo vượt mong đợi!"</p>
                <div class="rev-author">
                    <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Thanh Huyền">
                    <div>
                        <div class="rev-name">Chị Thanh Huyền</div>
                        <div class="rev-role">Khách hàng tháng 4/2025</div>
                    </div>
                </div>
            </div>
            <div class="rev-card sr d2">
                <div class="rev-stars">★★★★★</div>
                <p class="rev-text">"Chất ảnh điện ảnh, thái độ chuyên nghiệp đến từng chi tiết. Cảm ơn Sora đã cho
                    chúng tôi lưu giữ khoảnh khắc đẹp nhất đời."</p>
                <div class="rev-author">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Minh & Hà">
                    <div>
                        <div class="rev-name">Minh &amp; Hà</div>
                        <div class="rev-role">Cặp đôi tháng 3/2025</div>
                    </div>
                </div>
            </div>
            <div class="rev-card sr d3">
                <div class="rev-stars">★★★★★</div>
                <p class="rev-text">"Bộ ảnh cưới triệu view, ai cũng hỏi studio nào. Sora đỉnh thực sự – từ phim trường
                    đến ekip đều xuất sắc hết!"</p>
                <div class="rev-author">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Linh Nè">
                    <div>
                        <div class="rev-name">Linh Nè</div>
                        <div class="rev-role">KOL Wedding · 1.2M followers</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════ WORKFLOW ══════════════════════ -->
{{-- <section class="bg-warm">
    <div class="container">
        <div class="reveal-anim section-head" data-animate="fadeInUp" style="text-align:center">
            <span class="sec-tag" style="display:block">Quy trình</span>
            <h2 class="sec-title center">Từng bước cùng Sora</h2>
            <p class="sec-sub center">Quy trình 4 bước đơn giản, rõ ràng – để ngày trọng đại của bạn hoàn hảo nhất</p>
        </div>
        <div class="steps-row">
            <div class="step-item sr">
                <div class="step-circle">1</div>
                <h3>Trò chuyện &amp; ý tưởng</h3>
                <p>Lắng nghe câu chuyện tình yêu và phong cách bạn mơ ước</p>
            </div>
            <div class="step-item sr d2">
                <div class="step-circle">2</div>
                <h3>Tư vấn concept &amp; váy</h3>
                <p>Thiết kế bối cảnh, lựa chọn trang phục phù hợp nhất</p>
            </div>
            <div class="step-item sr d3">
                <div class="step-circle">3</div>
                <h3>Ngày chụp ảnh</h3>
                <p>Ekip chuyên nghiệp, không gian thoải mái và trọn niềm vui</p>
            </div>
            <div class="step-item sr d4">
                <div class="step-circle">4</div>
                <h3>Bàn giao &amp; lưu giữ</h3>
                <p>Chỉnh sửa tỉ mỉ, giao ảnh đúng hẹn, album bền đẹp vĩnh cửu</p>
            </div>
        </div>
    </div>
</section> --}}

<!-- ══════════════════════ CTA BAND ══════════════════════ -->
<section>
    <div class="container">
        <div class="cta-band sr sr-zoom">
            <div>
                <span class="cta-tag">Ưu đãi đặc biệt · Tháng này</span>
                <h2 class="cta-title">Nhận ngay ưu đãi 20%<br>cho tuần trăng mật</h2>
                <p class="cta-sub">Đặt lịch ngay hôm nay – tặng kèm 10 ảnh cưới ngoại cảnh &amp; album cao cấp miễn phí.
                    Áp dụng cho 30 cặp đôi đầu tiên trong tháng.</p>
                <div style="margin-top:28px;display:flex;gap:14px;flex-wrap:wrap">
                    <a href="#" class="btn btn-ghost">Đăng ký tư vấn miễn phí <i class="fas fa-heart"></i></a>
                    <a href="#" class="btn cta-chat-link" style="background:transparent;border:none;font-size:0.85rem">
                        <i class="fab fa-facebook-messenger"></i> Chat ngay Zalo
                    </a>
                </div>
            </div>
            <div class="cta-right">
                <div class="cta-offer">
                    <span class="cta-pct"><span class="count-up" data-count="20" data-suffix="%">0</span></span>
                    <div class="cta-pct-label">GIẢM NGAY HÔM NAY</div>
                </div>
                <a href="#" class="btn btn-solid" style="width:100%;justify-content:center">Xem bảng giá đầy đủ</a>
                <p class="cta-note">* Áp dụng đến 30/6/2025</p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════ BLOG ═══════════════════════════ -->
<section>
    <div class="container">
        <div class="reveal-anim section-head" data-animate="fadeInUp" style="text-align:center">
            <span class="sec-tag" style="display:block">Cập nhật</span>
            <h2 class="sec-title center">Blog cưới &amp; Cảm hứng</h2>
        </div>
        <div class="blog-grid">
            <div class="blog-card sr">
                <div class="blog-img"><img src="{{ asset('/user/demo.jpg') }}?t=420x230/f2e3da/b07d62?text=Bí+quyết"
                        alt="Blog 1">
                </div>
                <div class="blog-body">
                    <span class="blog-cat">Tư vấn cô dâu</span>
                    <h4>5 bí quyết chọn phong cách chụp ảnh cưới phù hợp nhất</h4>
                    <p>Phù hợp với từng dáng người, tính cách và câu chuyện tình yêu của bạn...</p>
                    <a href="#" class="read-more">Đọc tiếp <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="blog-card sr d2">
                <div class="blog-img"><img src="{{ asset('/user/demo.jpg') }}?t=420x230/eedfd6/b07d62?text=Xu+hướng"
                        alt="Blog 2">
                </div>
                <div class="blog-body">
                    <span class="blog-cat">Xu hướng 2025</span>
                    <h4>Top 3 concept phim trường được yêu thích nhất năm 2025</h4>
                    <p>Minimalism, vintage và cổ điển châu Âu đang dẫn đầu xu hướng ảnh cưới...</p>
                    <a href="#" class="read-more">Đọc tiếp <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="blog-card sr d3">
                <div class="blog-img"><img src="{{ asset('/user/demo.jpg') }}?t=420x230/f3e3db/b07d62?text=Phim+trường"
                        alt="Blog 3"></div>
                <div class="blog-body">
                    <span class="blog-cat">Tin Sora</span>
                    <h4>Ra mắt phim trường "Hoa Sữa" – lãng mạn mùa thu Hà Nội</h4>
                    <p>Không gian mới hoàn toàn dành riêng cho các cặp đôi yêu vẻ đẹp thu Hà Nội...</p>
                    <a href="#" class="read-more">Đọc tiếp <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

</div><!-- /.page-home -->

<!-- ══════════════════════ SUBPAGES ════════════════════ -->

<!-- PAGE: Câu chuyện của Sora -->
<div class="page-overlay" id="page-cau-chuyen">
    <div class="page-bar">
        <button class="page-bar-back" onclick="closePage('page-cau-chuyen')"><i class="fas fa-arrow-left"></i> Trang
            chủ</button>
        <span class="page-bar-title">Câu chuyện của Sora</span>
    </div>
    <div class="page-content">
        <div class="sp-hero">
            <span class="sec-tag">Về chúng tôi</span>
            <h1>Sora Studio – Nơi tình yêu<br>trở thành nghệ thuật</h1>
            <p>Hơn 102 năm đồng hành cùng hàng ngàn cặp đôi, Sora Studio được xây dựng từ niềm đam mê thuần khiết – lưu
                giữ những khoảnh khắc đẹp nhất bằng ngôn ngữ điện ảnh.</p>
        </div>
        <div class="sp-feature-grid">
            <div class="sp-feature">
                <div class="sp-feature-icon">📍</div>
                <h3>Ra đời tại Hà Nội · 2014</h3>
                <p>Khởi đầu từ một phòng chụp nhỏ ở Tây Hồ, Sora Studio dần trở thành địa chỉ ảnh cưới fine art được yêu
                    thích nhất Hà Nội. Chúng tôi đặt trọn trái tim vào từng tấm ảnh.</p>
            </div>
            <div class="sp-feature">
                <div class="sp-feature-icon">🏆</div>
                <h3>Giải thưởng & Ghi nhận</h3>
                <p>Top Studio Ảnh Cưới Hà Nội 2022–2025 theo bình chọn độc giả Weddingvn. Hơn 50 giải thưởng nhiếp ảnh
                    cưới trong nước và quốc tế.</p>
            </div>
            <div class="sp-feature">
                <div class="sp-feature-icon">🎬</div>
                <h3>Phong cách Fine Art Cinematic</h3>
                <p>Mỗi bức ảnh được xây dựng như một cảnh phim – ánh sáng tự nhiên, bố cục nghệ thuật, và cảm xúc chân
                    thật. Không dùng filter sẵn, không theo khuôn mẫu.</p>
            </div>
            <div class="sp-feature">
                <div class="sp-feature-icon">💍</div>
                <h3>Cam kết riêng tư tuyệt đối</h3>
                <p>Mỗi ngày chỉ phục vụ một cặp đôi tại mỗi phim trường. Không gian hoàn toàn riêng tư, đội ngũ tận tâm,
                    không bao giờ vội vàng.</p>
            </div>
        </div>
        <div style="text-align:center;margin-bottom:50px">
            <span class="sec-tag" style="display:block;margin-bottom:12px">Hành trình</span>
            <h2 class="sec-title center" style="margin-bottom:40px">Từ 2014 đến nay</h2>
        </div>
        <div class="sp-gallery">
            <div class="sp-gal-item"><img src="{{ asset('/user/demo.jpg') }}?t=400x560/f2e2d6/b07d62?text=2014+Khởi+đầu"
                    alt="2014"></div>
            <div class="sp-gal-item"><img
                    src="{{ asset('/user/demo.jpg') }}?t=400x380/ecdacf/b07d62?text=Studio+Rooftop" alt="Rooftop"></div>
            <div class="sp-gal-item"><img src="{{ asset('/user/demo.jpg') }}?t=400x480/f5e8e0/b07d62?text=Biệt+Thự+Sora"
                    alt="Villa"></div>
            <div class="sp-gal-item"><img src="{{ asset('/user/demo.jpg') }}?t=400x500/f3e7df/b07d62?text=Đội+ngũ+2025"
                    alt="Team">
            </div>
            <div class="sp-gal-item"><img
                    src="{{ asset('/user/demo.jpg') }}?t=400x340/f1e2d9/b07d62?text=Cặp+đôi+hạnh+phúc" alt="Couple">
            </div>
            <div class="sp-gal-item"><img src="{{ asset('/user/demo.jpg') }}?t=400x440/eedfd6/b07d62?text=Fine+Art"
                    alt="Art">
            </div>
        </div>
    </div>
</div>

<!-- PAGE: Đội ngũ nghệ sĩ -->
<div class="page-overlay" id="page-doi-ngu">
    <div class="page-bar">
        <button class="page-bar-back" onclick="closePage('page-doi-ngu')"><i class="fas fa-arrow-left"></i> Trang
            chủ</button>
        <span class="page-bar-title">Đội ngũ nghệ sĩ</span>
    </div>
    <div class="page-content">
        <div class="sp-hero">
            <span class="sec-tag">Con người · Tâm huyết</span>
            <h1>Đội ngũ nghệ sĩ<br>đứng sau mỗi khoảnh khắc</h1>
            <p>Mỗi thành viên Sora Studio là một nghệ nhân – được đào tạo bài bản, đam mê sáng tạo và luôn đặt cảm xúc
                của cặp đôi lên hàng đầu.</p>
        </div>
        <div class="team-grid">
            <div class="team-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=100x100/f2e2d6/b07d62?text=MA" class="team-avatar"
                    alt="Minh Anh">
                <div class="team-name">Minh Anh</div>
                <div class="team-role">Founder · Lead Photographer</div>
                <p class="team-bio">10+ năm chụp ảnh cưới fine art. Tốt nghiệp ĐH Nhiếp ảnh Paris. Chuyên gia ánh sáng
                    tự nhiên và storytelling.</p>
            </div>
            <div class="team-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=100x100/ecdacf/b07d62?text=HT" class="team-avatar"
                    alt="Hoàng Tuấn">
                <div class="team-name">Hoàng Tuấn</div>
                <div class="team-role">Senior Photographer</div>
                <p class="team-bio">Chuyên gia phong cách cinematic và ảnh ngoại cảnh. 8 năm kinh nghiệm, hơn 600 bộ ảnh
                    cưới.</p>
            </div>
            <div class="team-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=100x100/f5e8e0/b07d62?text=LH" class="team-avatar"
                    alt="Lan Hương">
                <div class="team-name">Lan Hương</div>
                <div class="team-role">Creative Director · Retoucher</div>
                <p class="team-bio">Nghệ sĩ chỉnh sửa ảnh cao cấp. Chuyên phục hồi màu sắc, nâng cấp trang phục và tạo
                    màu điện ảnh đặc trưng Sora.</p>
            </div>
            <div class="team-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=100x100/f3e3da/b07d62?text=TL" class="team-avatar"
                    alt="Thu Linh">
                <div class="team-name">Thu Linh</div>
                <div class="team-role">Stylist · Wardrobe Coordinator</div>
                <p class="team-bio">Chuyên tư vấn và phối đồ cưới. Am hiểu xu hướng thời trang cưới Châu Á và thị trường
                    Hanoi.</p>
            </div>
            <div class="team-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=100x100/f9efe8/b07d62?text=PN" class="team-avatar"
                    alt="Phú Nguyên">
                <div class="team-name">Phú Nguyên</div>
                <div class="team-role">Videographer</div>
                <p class="team-bio">Quay phim cinematic, dựng highlight video và phim tài liệu đám cưới. Chuyên thiết bị
                    drone và gimbal.</p>
            </div>
            <div class="team-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=100x100/f6e9e2/b07d62?text=BN" class="team-avatar"
                    alt="Bích Ngọc">
                <div class="team-name">Bích Ngọc</div>
                <div class="team-role">Coordinator · Client Care</div>
                <p class="team-bio">Điều phối mọi khâu chuẩn bị, tư vấn concept và đồng hành cùng cặp đôi từ lần đầu
                    liên hệ đến ngày nhận ảnh.</p>
            </div>
        </div>
    </div>
</div>

<!-- PAGE: Phim trường riêng -->
<div class="page-overlay" id="page-phim-truong">
    <div class="page-bar">
        <button class="page-bar-back" onclick="closePage('page-phim-truong')"><i class="fas fa-arrow-left"></i> Trang
            chủ</button>
        <span class="page-bar-title">Phim trường riêng</span>
    </div>
    <div class="page-content">
        <div class="sp-hero">
            <span class="sec-tag">Không gian độc quyền</span>
            <h1>Hai phim trường riêng –<br>Một cặp đôi mỗi ngày</h1>
            <p>Sora Studio sở hữu hai không gian chụp ảnh độc quyền tại Hà Nội. Mỗi ngày chỉ phục vụ một cặp đôi tại mỗi
                địa điểm – đảm bảo không gian riêng tư, thoải mái và sáng tạo nhất.</p>
        </div>
        <div class="sp-feature-grid" style="margin-bottom:48px">
            <div class="sp-feature">
                <div class="sp-feature-icon">🌅</div>
                <h3>Sora Rooftop – Hồ Tây</h3>
                <p>Tầm nhìn panoramic hướng Hồ Tây. Ánh sáng tự nhiên golden hour độc đáo. Không gian sân thượng hiện
                    đại với backdrop Hà Nội đặc trưng. Lý tưởng cho concept ngoại cảnh và modern romance.</p>
            </div>
            <div class="sp-feature">
                <div class="sp-feature-icon">🏛️</div>
                <h3>Biệt Thự Sora – Colonial Villa</h3>
                <p>Kiến trúc Pháp cổ điển đầu thế kỷ 20. Cầu thang đá xưa, vòm cổng, hành lang trắng thanh lịch. Kho đạo
                    cụ xa hoa với hơn 200 items. Lý tưởng cho concept cổ điển Châu Âu và vintage.</p>
            </div>
        </div>
        <div class="sp-gallery">
            <div class="sp-gal-item"><img
                    src="{{ asset('/user/demo.jpg') }}?t=400x580/f2e8e0/b07d62?text=Rooftop+View+Hồ+Tây" alt="Rooftop">
            </div>
            <div class="sp-gal-item"><img
                    src="{{ asset('/user/demo.jpg') }}?t=400x380/ecdacf/b07d62?text=Golden+Hour+Studio"
                    alt="Golden Hour"></div>
            <div class="sp-gal-item"><img
                    src="{{ asset('/user/demo.jpg') }}?t=400x460/f5e8e0/b07d62?text=Biệt+Thự+Colonial" alt="Villa">
            </div>
            <div class="sp-gal-item"><img
                    src="{{ asset('/user/demo.jpg') }}?t=400x340/f3e7df/b07d62?text=Cầu+Thang+Đá+Cổ" alt="Stairs"></div>
            <div class="sp-gal-item"><img
                    src="{{ asset('/user/demo.jpg') }}?t=400x500/f1e2d9/b07d62?text=Không+gian+riêng+tư" alt="Private">
            </div>
            <div class="sp-gal-item"><img
                    src="{{ asset('/user/demo.jpg') }}?t=400x420/eedfd6/b07d62?text=Đạo+cụ+cao+cấp" alt="Props"></div>
        </div>
    </div>
</div>

<!-- PAGE: News Concept / Concept mới nhất -->
<div class="page-overlay" id="page-concept">
    <div class="page-bar">
        <button class="page-bar-back" onclick="closePage('page-concept')"><i class="fas fa-arrow-left"></i> Trang
            chủ</button>
        <span class="page-bar-title">News Concept</span>
    </div>
    <div class="page-content">
        <div class="sp-hero">
            <span class="sec-tag">Concept mới nhất · 2025</span>
            <h1>Những concept độc bản<br>chỉ có tại Sora</h1>
            <p>Mỗi mùa Sora Studio ra mắt những concept ảnh cưới hoàn toàn mới – sáng tạo, không lặp lại và mang dấu ấn
                riêng. Đặt lịch sớm để chọn concept yêu thích.</p>
        </div>
        <div class="concept-grid">
            <div class="concept-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=420x340/f2e2d6/b07d62?text=Midsummer+Dream" alt="Midsummer">
                <div class="concept-card-body">
                    <h4>Midsummer Dream</h4>
                    <p>Mới 2025 · Sora Rooftop</p>
                </div>
            </div>
            <div class="concept-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=420x340/ecdacf/b07d62?text=Versailles+Blanc" alt="Versailles">
                <div class="concept-card-body">
                    <h4>Versailles Blanc</h4>
                    <p>Hot · Biệt Thự Sora</p>
                </div>
            </div>
            <div class="concept-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=420x340/f5e8e0/b07d62?text=Sakura+Romance" alt="Sakura">
                <div class="concept-card-body">
                    <h4>Sakura Romance</h4>
                    <p>Mới 2025 · Sora Rooftop</p>
                </div>
            </div>
            <div class="concept-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=420x340/f3e7df/b07d62?text=Dark+Luxury" alt="Dark Luxury">
                <div class="concept-card-body">
                    <h4>Dark Luxury</h4>
                    <p>Giới hạn · Biệt Thự Sora</p>
                </div>
            </div>
            <div class="concept-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=420x340/f1e2d9/b07d62?text=Hoa+Sữa+Autumn" alt="Hoa Sua">
                <div class="concept-card-body">
                    <h4>Hoa Sữa Autumn</h4>
                    <p>Thu 2025 · Hồ Tây</p>
                </div>
            </div>
            <div class="concept-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=420x340/eedfd6/b07d62?text=Modern+Minimal" alt="Minimal">
                <div class="concept-card-body">
                    <h4>Modern Minimal</h4>
                    <p>Thường xuyên · Both Studios</p>
                </div>
            </div>
        </div>
        <div style="text-align:center">
            <a href="#" onclick="openPage('page-dat-lich');return false" class="btn btn-solid"
                style="font-size:0.9rem">Đặt concept ngay <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</div>

<!-- PAGE: Váy cưới designer -->
<div class="page-overlay" id="page-vay-cuoi">
    <div class="page-bar">
        <button class="page-bar-back" onclick="closePage('page-vay-cuoi')"><i class="fas fa-arrow-left"></i> Trang
            chủ</button>
        <span class="page-bar-title">Váy cưới designer</span>
    </div>
    <div class="page-content">
        <div class="sp-hero">
            <span class="sec-tag">Trang phục · Bộ sưu tập 2025</span>
            <h1>Hơn 500+ thiết kế váy cưới<br>cao cấp nhập khẩu</h1>
            <p>Từ váy xòe công chúa đến váy ôm hiện đại, váy voan bay bổng đến ren cổ điển – kho váy Sora là kho tàng
                dành riêng cho nàng dâu.</p>
        </div>
        <div class="dress-grid">
            <div class="dress-card">
                <div class="dress-img"><img src="{{ asset('/user/demo.jpg') }}?t=300x320/f9f0eb/b07d62?text=Lumière+01"
                        alt="dress"></div>
                <div class="dress-body">
                    <div class="dress-name">Lumière 01</div>
                    <div class="dress-type">Váy xòe bồng · Shanghai Import</div>
                </div>
            </div>
            <div class="dress-card">
                <div class="dress-img"><img
                        src="{{ asset('/user/demo.jpg') }}?t=300x320/f5e8e0/b07d62?text=Blanc+Couture" alt="dress">
                </div>
                <div class="dress-body">
                    <div class="dress-name">Blanc Couture</div>
                    <div class="dress-type">Váy đuôi cá · France Design</div>
                </div>
            </div>
            <div class="dress-card">
                <div class="dress-img"><img
                        src="{{ asset('/user/demo.jpg') }}?t=300x320/ecdacf/b07d62?text=Rose+Princess" alt="dress">
                </div>
                <div class="dress-body">
                    <div class="dress-name">Rose Princess</div>
                    <div class="dress-type">Váy công chúa · Hoa 3D</div>
                </div>
            </div>
            <div class="dress-card">
                <div class="dress-img"><img src="{{ asset('/user/demo.jpg') }}?t=300x320/f2e2d6/b07d62?text=Sakura+Veil"
                        alt="dress"></div>
                <div class="dress-body">
                    <div class="dress-name">Sakura Veil</div>
                    <div class="dress-type">Váy ren · Korea Bridal</div>
                </div>
            </div>
            <div class="dress-card">
                <div class="dress-img"><img
                        src="{{ asset('/user/demo.jpg') }}?t=300x320/f3e3db/b07d62?text=Ivory+Minimal" alt="dress">
                </div>
                <div class="dress-body">
                    <div class="dress-name">Ivory Minimal</div>
                    <div class="dress-type">Váy suông · Modern Cut</div>
                </div>
            </div>
            <div class="dress-card">
                <div class="dress-img"><img
                        src="{{ asset('/user/demo.jpg') }}?t=300x320/f7eae3/b07d62?text=Vintage+Lace" alt="dress"></div>
                <div class="dress-body">
                    <div class="dress-name">Vintage Lace</div>
                    <div class="dress-type">Váy ren cổ điển · European</div>
                </div>
            </div>
            <div class="dress-card">
                <div class="dress-img"><img
                        src="{{ asset('/user/demo.jpg') }}?t=300x320/f6e9e2/b07d62?text=Crystal+Glam" alt="dress"></div>
                <div class="dress-body">
                    <div class="dress-name">Crystal Glam</div>
                    <div class="dress-type">Váy đính đá · High Couture</div>
                </div>
            </div>
            <div class="dress-card">
                <div class="dress-img"><img src="{{ asset('/user/demo.jpg') }}?t=300x320/f0ddd4/b07d62?text=Flora+Dream"
                        alt="dress"></div>
                <div class="dress-body">
                    <div class="dress-name">Flora Dream</div>
                    <div class="dress-type">Váy hoa · Boho Style</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGE: Áo dài cô dâu -->
<div class="page-overlay" id="page-ao-dai">
    <div class="page-bar">
        <button class="page-bar-back" onclick="closePage('page-ao-dai')"><i class="fas fa-arrow-left"></i> Trang
            chủ</button>
        <span class="page-bar-title">Áo dài cô dâu</span>
    </div>
    <div class="page-content">
        <div class="sp-hero">
            <span class="sec-tag">Trang phục · Thuần Việt</span>
            <h1>Áo dài cô dâu – Vẻ đẹp<br>thuần Việt tinh tế</h1>
            <p>Bộ sưu tập áo dài cưới cao cấp, thiết kế riêng cho Sora Studio. Kết hợp hoàn hảo giữa truyền thống và
                hiện đại, tôn lên vẻ đẹp duyên dáng của người phụ nữ Việt.</p>
        </div>
        <div class="dress-grid">
            <div class="dress-card">
                <div class="dress-img"><img
                        src="{{ asset('/user/demo.jpg') }}?t=300x320/fce8dd/b07d62?text=Áo+dài+đỏ+thêu" alt="ao dai">
                </div>
                <div class="dress-body">
                    <div class="dress-name">Hồng Nhung</div>
                    <div class="dress-type">Áo dài đỏ · Thêu hoa sen</div>
                </div>
            </div>
            <div class="dress-card">
                <div class="dress-img"><img
                        src="{{ asset('/user/demo.jpg') }}?t=300x320/f5e8e0/b07d62?text=Áo+dài+trắng" alt="ao dai">
                </div>
                <div class="dress-body">
                    <div class="dress-name">Bạch Liên</div>
                    <div class="dress-type">Áo dài trắng · Ren cao cấp</div>
                </div>
            </div>
            <div class="dress-card">
                <div class="dress-img"><img src="{{ asset('/user/demo.jpg') }}?t=300x320/ecdacf/b07d62?text=Áo+dài+vàng"
                        alt="ao dai"></div>
                <div class="dress-body">
                    <div class="dress-name">Hoàng Kim</div>
                    <div class="dress-type">Áo dài vàng · Gấm thủ công</div>
                </div>
            </div>
            <div class="dress-card">
                <div class="dress-img"><img src="{{ asset('/user/demo.jpg') }}?t=300x320/f2e2d6/b07d62?text=Áo+dài+hồng"
                        alt="ao dai"></div>
                <div class="dress-body">
                    <div class="dress-name">Đào Hương</div>
                    <div class="dress-type">Áo dài hồng · Tơ Hà Đông</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGE: Vest chú rể -->
<div class="page-overlay" id="page-vest">
    <div class="page-bar">
        <button class="page-bar-back" onclick="closePage('page-vest')"><i class="fas fa-arrow-left"></i> Trang
            chủ</button>
        <span class="page-bar-title">Vest chú rể</span>
    </div>
    <div class="page-content">
        <div class="sp-hero">
            <span class="sec-tag">Trang phục · Chú rể</span>
            <h1>Vest chú rể – Lịch lãm<br>và đẳng cấp</h1>
            <p>Bộ sưu tập vest cưới cao cấp dành cho chú rể. Từ classic đen truyền thống đến navy, champagne tinh tế –
                tôn dáng và hoàn toàn phối hợp cùng váy cô dâu.</p>
        </div>
        <div class="dress-grid">
            <div class="dress-card">
                <div class="dress-img"><img
                        src="{{ asset('/user/demo.jpg') }}?t=300x320/2a1f17/b07d62?text=Classic+Black" alt="vest"></div>
                <div class="dress-body">
                    <div class="dress-name">Classic Black</div>
                    <div class="dress-type">Vest đen · 3 mảnh</div>
                </div>
            </div>
            <div class="dress-card">
                <div class="dress-img"><img
                        src="{{ asset('/user/demo.jpg') }}?t=300x320/3a4a5a/b07d62?text=Midnight+Navy" alt="vest"></div>
                <div class="dress-body">
                    <div class="dress-name">Midnight Navy</div>
                    <div class="dress-type">Vest xanh đêm · Slim Fit</div>
                </div>
            </div>
            <div class="dress-card">
                <div class="dress-img"><img
                        src="{{ asset('/user/demo.jpg') }}?t=300x320/d4c4a8/8f5e46?text=Champagne+Gold" alt="vest">
                </div>
                <div class="dress-body">
                    <div class="dress-name">Champagne Gold</div>
                    <div class="dress-type">Vest vàng champagne · Luxury</div>
                </div>
            </div>
            <div class="dress-card">
                <div class="dress-img"><img src="{{ asset('/user/demo.jpg') }}?t=300x320/8a8a8a/fff?text=Dove+Grey"
                        alt="vest">
                </div>
                <div class="dress-body">
                    <div class="dress-name">Dove Grey</div>
                    <div class="dress-type">Vest xám · Italian Cut</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGE: Album thực tế -->
<div class="page-overlay" id="page-album">
    <div class="page-bar">
        <button class="page-bar-back" onclick="closePage('page-album')"><i class="fas fa-arrow-left"></i> Trang
            chủ</button>
        <span class="page-bar-title">Album thực tế</span>
    </div>
    <div class="page-content">
        <div class="sp-hero">
            <span class="sec-tag">Khách hàng · Bộ ảnh thực tế</span>
            <h1>Những câu chuyện tình yêu<br>được Sora ghi lại</h1>
            <p>Đây là những bộ ảnh cưới thực tế từ khách hàng – không chỉnh sửa quảng cáo, không sắp đặt giả tạo. Đây là
                sự thật đẹp nhất về những ngày hạnh phúc.</p>
        </div>
        <div class="album-grid">
            <div class="album-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=580x380/f2e2d6/b07d62?text=Minh+Châu+%26+Hoàng+Anh"
                    alt="Album 1">
                <div class="album-card-info">
                    <h4>Minh Châu &amp; Hoàng Anh</h4>
                    <p>Sora Rooftop · Concept Midsummer Dream · Tháng 3/2025</p>
                </div>
            </div>
            <div class="album-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=580x380/ecdacf/b07d62?text=Lan+Phương+%26+Đức+Minh"
                    alt="Album 2">
                <div class="album-card-info">
                    <h4>Lan Phương &amp; Đức Minh</h4>
                    <p>Biệt Thự Sora · Concept Versailles Blanc · Tháng 2/2025</p>
                </div>
            </div>
            <div class="album-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=580x380/f5e8e0/b07d62?text=Thu+Hà+%26+Minh+Quân"
                    alt="Album 3">
                <div class="album-card-info">
                    <h4>Thu Hà &amp; Minh Quân</h4>
                    <p>Both Studios · Concept Rose Princess · Tháng 1/2025</p>
                </div>
            </div>
            <div class="album-card">
                <img src="{{ asset('/user/demo.jpg') }}?t=580x380/f3e7df/b07d62?text=Bảo+Châu+%26+Tiến+Đạt"
                    alt="Album 4">
                <div class="album-card-info">
                    <h4>Bảo Châu &amp; Tiến Đạt</h4>
                    <p>Sora Rooftop · Áo dài truyền thống · Tháng 12/2024</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGE: Review & Cảm nhận -->
<div class="page-overlay" id="page-review">
    <div class="page-bar">
        <button class="page-bar-back" onclick="closePage('page-review')"><i class="fas fa-arrow-left"></i> Trang
            chủ</button>
        <span class="page-bar-title">Review &amp; Cảm nhận</span>
    </div>
    <div class="page-content">
        <div class="sp-hero">
            <span class="sec-tag">Đánh giá · 5 sao</span>
            <h1>Hàng ngàn cặp đôi<br>chia sẻ cảm nhận</h1>
            <p>Không có lời nào trân trọng hơn lời cảm ơn từ khách hàng. Đây là những gì các cặp đôi nói về trải nghiệm
                tại Sora Studio.</p>
        </div>
        <div class="review-grid" style="margin-bottom:60px">
            <div class="rev-card">
                <div class="rev-stars">★★★★★</div>
                <p class="rev-text">"Không có từ nào đủ để diễn tả sự hài lòng. Ekip cực kỳ chuyên nghiệp và tận tình.
                    Ảnh đẹp hơn mong đợi rất nhiều, mỗi tấm như một tác phẩm nghệ thuật."</p>
                <div class="rev-author">
                    <img src="{{ asset('/user/demo.jpg') }}?t=48x48/f2e2d6/b07d62?text=MC" alt="Minh Châu">
                    <div>
                        <div class="rev-name">Minh Châu &amp; Hoàng Anh</div>
                        <div class="rev-role">Cặp đôi · Tháng 3/2025</div>
                    </div>
                </div>
            </div>
            <div class="rev-card">
                <div class="rev-stars">★★★★★</div>
                <p class="rev-text">"Sora Studio thực sự khác biệt. Phim trường đẹp và riêng tư hoàn toàn. Cảm giác như
                    được phục vụ riêng, không bị chia sẻ với ai. Rất đáng tiền!"</p>
                <div class="rev-author">
                    <img src="{{ asset('/user/demo.jpg') }}?t=48x48/ecdacf/b07d62?text=LP" alt="Lan Phương">
                    <div>
                        <div class="rev-name">Lan Phương &amp; Đức Minh</div>
                        <div class="rev-role">Cặp đôi · Tháng 2/2025</div>
                    </div>
                </div>
            </div>
            <div class="rev-card">
                <div class="rev-stars">★★★★★</div>
                <p class="rev-text">"Quá trình từ tư vấn đến nhận ảnh đều rất suôn sẻ và chuyên nghiệp. Váy cưới đẹp
                    không thua showroom nước ngoài. Chị stylist rất hiểu ý mình!"</p>
                <div class="rev-author">
                    <img src="{{ asset('/user/demo.jpg') }}?t=48x48/f5e8e0/b07d62?text=TH" alt="Thu Hà">
                    <div>
                        <div class="rev-name">Thu Hà &amp; Minh Quân</div>
                        <div class="rev-role">Cặp đôi · Tháng 1/2025</div>
                    </div>
                </div>
            </div>
            <div class="rev-card">
                <div class="rev-stars">★★★★★</div>
                <p class="rev-text">"Mình đã đến rất nhiều studio nhưng Sora là nơi duy nhất mình cảm thấy thực sự thoải
                    mái. Anh chị ekip luôn biết cách khai thác cảm xúc tự nhiên nhất."</p>
                <div class="rev-author">
                    <img src="{{ asset('/user/demo.jpg') }}?t=48x48/f3e7df/b07d62?text=BC" alt="Bảo Châu">
                    <div>
                        <div class="rev-name">Bảo Châu &amp; Tiến Đạt</div>
                        <div class="rev-role">Cặp đôi · Tháng 12/2024</div>
                    </div>
                </div>
            </div>
            <div class="rev-card">
                <div class="rev-stars">★★★★★</div>
                <p class="rev-text">"Ảnh retouch cực đẹp, váy từ cũ thành như mới. Mình mặc lại váy cũ của mẹ nhưng qua
                    tay Sora trông như thiết kế mới hoàn toàn. Xúc động lắm!"</p>
                <div class="rev-author">
                    <img src="{{ asset('/user/demo.jpg') }}?t=48x48/f9efe8/b07d62?text=HN" alt="Hà Nhi">
                    <div>
                        <div class="rev-name">Hà Nhi &amp; Gia Bảo</div>
                        <div class="rev-role">Cặp đôi · Tháng 11/2024</div>
                    </div>
                </div>
            </div>
            <div class="rev-card">
                <div class="rev-stars">★★★★★</div>
                <p class="rev-text">"Studio đẹp như resort, ekip vui vẻ chuyên nghiệp. Ảnh giao đúng hẹn và còn nhiều
                    hơn số lượng cam kết. Chắc chắn sẽ giới thiệu cho bạn bè!"</p>
                <div class="rev-author">
                    <img src="{{ asset('/user/demo.jpg') }}?t=48x48/f6e9e2/b07d62?text=NK" alt="Nhã Khanh">
                    <div>
                        <div class="rev-name">Nhã Khanh &amp; Phú Hải</div>
                        <div class="rev-role">KOL Wedding · 1.2M followers</div>
                    </div>
                </div>
            </div>
        </div>
        <div style="text-align:center">
            <a href="https://g.page/r/sora-studio" class="btn btn-outline" target="_blank"><i class="fab fa-google"></i>
                Xem đánh giá Google</a>
        </div>
    </div>
</div>

<!-- PAGE: Blog cưới -->
<div class="page-overlay" id="page-blog">
    <div class="page-bar">
        <button class="page-bar-back" onclick="closePage('page-blog')"><i class="fas fa-arrow-left"></i> Trang
            chủ</button>
        <span class="page-bar-title">Blog cưới</span>
    </div>
    <div class="page-content">
        <div class="sp-hero">
            <span class="sec-tag">Kiến thức · Cảm hứng</span>
            <h1>Blog cưới Sora – Kho<br>kiến thức cho nàng dâu</h1>
            <p>Từ bí quyết chọn concept, cách chọn váy đến xu hướng ảnh cưới mới nhất – Sora Blog là người bạn đồng hành
                tin cậy trong hành trình chuẩn bị đám cưới.</p>
        </div>
        <div class="blog-full-grid">
            <div class="blog-card">
                <div class="blog-img"><img
                        src="{{ asset('/user/demo.jpg') }}?t=420x230/f2e3da/b07d62?text=Bí+quyết+chọn+váy" alt="Blog 1">
                </div>
                <div class="blog-body">
                    <span class="blog-cat">Tư vấn cô dâu</span>
                    <h4>5 bí quyết chọn phong cách chụp ảnh cưới phù hợp nhất</h4>
                    <p>Phù hợp với từng dáng người, tính cách và câu chuyện tình yêu của bạn...</p>
                    <a href="#" class="read-more">Đọc tiếp <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="blog-card">
                <div class="blog-img"><img
                        src="{{ asset('/user/demo.jpg') }}?t=420x230/eedfd6/b07d62?text=Xu+hướng+2025" alt="Blog 2">
                </div>
                <div class="blog-body">
                    <span class="blog-cat">Xu hướng 2025</span>
                    <h4>Top 3 concept phim trường được yêu thích nhất năm 2025</h4>
                    <p>Minimalism, vintage và cổ điển châu Âu đang dẫn đầu xu hướng...</p>
                    <a href="#" class="read-more">Đọc tiếp <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="blog-card">
                <div class="blog-img"><img
                        src="{{ asset('/user/demo.jpg') }}?t=420x230/f3e3db/b07d62?text=Phim+trường+mới" alt="Blog 3">
                </div>
                <div class="blog-body">
                    <span class="blog-cat">Tin Sora</span>
                    <h4>Ra mắt phim trường "Hoa Sữa" – lãng mạn mùa thu Hà Nội</h4>
                    <p>Không gian mới dành riêng cho những cặp đôi yêu vẻ đẹp thu...</p>
                    <a href="#" class="read-more">Đọc tiếp <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="blog-card">
                <div class="blog-img"><img src="{{ asset('/user/demo.jpg') }}?t=420x230/f5e8e0/b07d62?text=Chọn+studio"
                        alt="Blog 4"></div>
                <div class="blog-body">
                    <span class="blog-cat">Hướng dẫn</span>
                    <h4>Cách chọn studio ảnh cưới phù hợp – 7 tiêu chí cần biết</h4>
                    <p>Đừng chỉ nhìn vào giá – đây là những gì thực sự quan trọng...</p>
                    <a href="#" class="read-more">Đọc tiếp <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="blog-card">
                <div class="blog-img"><img src="{{ asset('/user/demo.jpg') }}?t=420x230/f2e2d6/b07d62?text=Retouch+ảnh"
                        alt="Blog 5"></div>
                <div class="blog-body">
                    <span class="blog-cat">Hậu kỳ</span>
                    <h4>Quy trình retouch ảnh cưới tại Sora – Từ bình thường đến xuất sắc</h4>
                    <p>Sự kỳ diệu của việc chỉnh sửa ảnh chuyên nghiệp được giải thích...</p>
                    <a href="#" class="read-more">Đọc tiếp <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="blog-card">
                <div class="blog-img"><img src="{{ asset('/user/demo.jpg') }}?t=420x230/ecdacf/b07d62?text=Ngày+chụp"
                        alt="Blog 6">
                </div>
                <div class="blog-body">
                    <span class="blog-cat">Chuẩn bị</span>
                    <h4>Ngày chụp ảnh cưới cần chuẩn bị gì? Checklist đầy đủ từ A-Z</h4>
                    <p>Để ngày chụp diễn ra suôn sẻ và không bị quên bất cứ thứ gì...</p>
                    <a href="#" class="read-more">Đọc tiếp <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGE: FAQ -->
<div class="page-overlay" id="page-faq">
    <div class="page-bar">
        <button class="page-bar-back" onclick="closePage('page-faq')"><i class="fas fa-arrow-left"></i> Trang
            chủ</button>
        <span class="page-bar-title">Câu hỏi thường gặp</span>
    </div>
    <div class="page-content">
        <div class="sp-hero">
            <span class="sec-tag">Hỗ trợ · FAQ</span>
            <h1>Những câu hỏi thường gặp<br>về dịch vụ Sora Studio</h1>
        </div>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-q" onclick="toggleFaq(this)">Sora Studio phục vụ bao nhiêu cặp đôi mỗi ngày? <span
                        class="faq-icon">+</span></div>
                <div class="faq-a">Sora Studio chỉ phục vụ <strong>1 cặp đôi tại mỗi phim trường mỗi ngày</strong>. Sora
                    Rooftop và Biệt Thự Sora hoạt động độc lập, đảm bảo bạn có trọn vẹn không gian riêng tư và ekip tập
                    trung 100% cho bạn.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="toggleFaq(this)">Sora có nhận váy cưới của khách hàng mang đến chụp không?
                    <span class="faq-icon">+</span></div>
                <div class="faq-a">Có! Sora hoàn toàn hỗ trợ khách mang váy riêng đến chụp. Đội ngũ retouch của Sora sẽ
                    xử lý hậu kỳ để váy cũ trông như váy mới – đây là một trong những thế mạnh đặc biệt của Sora.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="toggleFaq(this)">Thời gian nhận ảnh là bao lâu? <span
                        class="faq-icon">+</span></div>
                <div class="faq-a">Thông thường từ <strong>7–14 ngày làm việc</strong> sau ngày chụp. Sora cũng có dịch
                    vụ "ảnh liền tay" – nhận ảnh sơ bộ ngay trong ngày chụp. Album vật lý in ấn thường thêm 7–10 ngày.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="toggleFaq(this)">Có cần đặt cọc để giữ ngày không? <span
                        class="faq-icon">+</span></div>
                <div class="faq-a">Có. Để giữ lịch chụp, bạn cần <strong>đặt cọc 30%</strong> tổng giá trị hợp đồng.
                    Thanh toán phần còn lại trước ngày chụp 3 ngày. Cọc không hoàn trả nếu hủy trong vòng 7 ngày trước
                    ngày chụp.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="toggleFaq(this)">Sora có chụp ngoại cảnh không? <span
                        class="faq-icon">+</span></div>
                <div class="faq-a">Ngoài 2 phim trường riêng, Sora có dịch vụ chụp ngoại cảnh Hà Nội và một số địa điểm
                    lân cận. Liên hệ để được báo giá chi tiết theo concept và địa điểm bạn mong muốn.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q" onclick="toggleFaq(this)">Có thể thay đổi concept sau khi đặt lịch không? <span
                        class="faq-icon">+</span></div>
                <div class="faq-a">Được! Bạn có thể điều chỉnh concept trước ngày chụp <strong>ít nhất 3 ngày</strong>.
                    Một số concept đặc biệt (giới hạn, theo mùa) có thể cần đặt trước lâu hơn.</div>
            </div>
        </div>
        <div style="text-align:center">
            <p style="color:var(--ink-soft);margin-bottom:20px;font-size:0.9rem">Còn câu hỏi khác? Liên hệ trực tiếp với
                chúng tôi</p>
            <a href="#" onclick="openPage('page-dat-lich');return false" class="btn btn-solid">Chat với Sora ngay <i
                    class="fas fa-comment"></i></a>
        </div>
    </div>
</div>

<!-- PAGE: Đặt lịch tư vấn -->
<div class="page-overlay" id="page-dat-lich">
    <div class="page-bar">
        <button class="page-bar-back" onclick="closePage('page-dat-lich')"><i class="fas fa-arrow-left"></i> Trang
            chủ</button>
        <span class="page-bar-title">Đặt lịch tư vấn</span>
    </div>
    <div class="page-content">
        <div class="sp-hero">
            <span class="sec-tag">Liên hệ · Đặt lịch</span>
            <h1>Bắt đầu hành trình<br>cùng Sora Studio</h1>
            <p>Điền form dưới đây để đặt lịch tư vấn miễn phí. Đội ngũ Sora sẽ liên hệ trong vòng 2 giờ làm việc để tư
                vấn chi tiết concept và báo giá phù hợp nhất.</p>
        </div>
        <div class="contact-grid">
            <div>
                <div class="contact-info-item">
                    <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <h4>Sora Rooftop</h4>
                        <p>Phố Xuân Diệu, Tây Hồ, Hà Nội<br>View Hồ Tây · Golden Hour</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <h4>Biệt Thự Sora</h4>
                        <p>Ngõ 55 Quảng An, Tây Hồ, Hà Nội<br>Colonial Villa · Cổ điển Pháp</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                    <div>
                        <h4>Hotline</h4>
                        <p>0909 123 456<br>8:00 – 20:00 · Tất cả các ngày</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="contact-icon"><i class="fab fa-facebook-messenger"></i></div>
                    <div>
                        <h4>Zalo / Messenger</h4>
                        <p>Phản hồi nhanh trong 30 phút<br>trong giờ làm việc</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <h4>Email</h4>
                        <p>hello@sorastudio.vn</p>
                    </div>
                </div>
            </div>
            <div>
                @include('user.partials.booking-form', ['bookingFormContext' => 'home'])
            </div>
        </div>
    </div>
</div>

@endsection

@php
$shouldAutoOpenBookingOverlay = old('booking_form_context') === 'home' || session('booking_form_context') === 'home';
@endphp

@if ($shouldAutoOpenBookingOverlay)
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof openPage === 'function') {
            openPage('page-dat-lich');
        }
    });

</script>
@endpush
@endif
