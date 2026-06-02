<!-- ══════════════════════ HEADER ══════════════════════ -->
<header id="mainHeader">
  <div class="container">
    <nav class="navbar">
      <a href="{{ route('user.home') }}" class="logo" style="text-decoration:none;color:inherit">
        <h2>Sora Bridal</h2>
        <span>Bridal – make up – studio</span>
      </a>
      <button class="menu-toggle" id="menuToggle" aria-label="Menu toggle">
        <i class="fas fa-bars"></i>
      </button>
      <ul class="nav-links" id="navLinks">
        <li><a href="{{ route('user.home') }}" class="{{ request()->routeIs('user.home') ? 'active' : '' }}">Trang chủ</a></li>
        <li><a href="{{ route('user.dich-vu') }}" class="{{ request()->routeIs('user.dich-vu') ? 'active' : '' }}">Dịch vụ</a></li>
        <li><a href="{{ route('user.news-concept') }}" class="{{ request()->routeIs('user.news-concept') ? 'active' : '' }}">News Concept</a></li>
        <li><a href="{{ route('user.trang-phuc') }}" class="{{ request()->routeIs('user.trang-phuc') ? 'active' : '' }}">Trang phục</a></li>
        <li><a href="{{ route('user.khach-hang') }}" class="{{ request()->routeIs('user.khach-hang') ? 'active' : '' }}">Khách hàng</a></li>
        <li><a href="{{ route('user.blog-cuoi') }}" class="{{ request()->routeIs('user.blog-cuoi') ? 'active' : '' }}">Blog cưới</a></li>
        <li>
          <a href="{{ route('user.dat-lich') }}" class="btn btn-solid" style="padding:4px 16px;font-size:18px;">
            <i class="fas fa-phone-alt"></i> Đặt lịch
          </a>
        </li>
      </ul>
    </nav>
  </div>
</header>
  