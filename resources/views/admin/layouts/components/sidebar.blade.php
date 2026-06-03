        <aside id="layout-menu" class="layout-menu menu-vertical menu">
          <div class="app-brand demo ">
            <a href="{{ route('admin.index') }}" class="app-brand-link">
              <span class="app-brand-logo demo">
                <span class="text-primary">
                  <svg width="44" height="30" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <text
                      x="16"
                      y="17"
                      text-anchor="middle"
                      font-family="system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif"
                      font-size="17"
                      font-weight="700"
                      fill="currentColor">P</text>
                  </svg>
                </span>
              </span>
              <span class="app-brand-text demo menu-text fw-bold ms-3">Pandio</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
              <i class="icon-base ti tabler-x d-block d-xl-none"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            @php
              // Menu sidebar: lọc theo vai_tro.ds_menu (user.role → ma_vai_tro), build tại AppServiceProvider
              $menusFromSidebarDsMenu = $sidebarMenuItems ?? [];
            @endphp
            @foreach($menusFromSidebarDsMenu as $item)
            {{-- <li class="menu-header small">
              <span class="menu-header-text">{{ $item['label'] }}</span>
            </li> --}}
              @if ($item['type'] === 'single')
                <li class="menu-item {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                  <a href="{{ route($item['route']) }}" class="menu-link">
                    <i class="menu-icon icon-base {{ $item['icon'] ?? 'ti tabler-circle' }}"></i>
                    {{-- <div>{{ $loop->iteration }}. {{ $item['label'] }}</div> --}}
                    <div>{{ $item['label'] }}</div>
                  </a>
                </li>
              @elseif ($item['type'] === 'group' && !empty($item['children']))
                @php
                  $routePrefixes = array_values(array_filter(array_merge(
                      isset($item['route_prefix']) && $item['route_prefix'] !== ''
                          ? [$item['route_prefix']]
                          : [],
                      $item['route_prefixes'] ?? []
                  )));
                  $isOpen = false;
                  foreach ($routePrefixes as $p) {
                      $pattern = str_ends_with($p, '*') ? $p : rtrim($p, '.') . '.*';
                      if (request()->routeIs($pattern)) {
                          $isOpen = true;
                          break;
                      }
                  }
                @endphp
                <li class="menu-item {{ $isOpen ? 'active open' : '' }}">
                  <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base {{ $item['icon'] ?? 'ti tabler-circle' }}"></i>
                    {{-- <div>{{ $loop->iteration }}. {{ $item['label'] }}</div> --}}
                    <div>{{ $item['label'] }}</div>
                  </a>
                  <ul class="menu-sub">
                    @foreach ($item['children'] as $child)
                      <li class="menu-item {{ request()->routeIs($child['route']) ? 'active' : '' }}">
                        <a href="{{ route($child['route']) }}" class="menu-link">
                          <i class="menu-icon icon-base {{ $child['icon'] ?? $item['icon'] ?? 'ti tabler-circle' }}"></i>
                          <div>{{ $child['label'] }}</div>
                        </a>
                      </li>
                    @endforeach
                  </ul>
                </li>
              @endif
            @endforeach
          </ul>
          <script src="../../assets/js/admin-menu-spread.js"></script>
          <script>
            if (window.AdminMenuSpread) {
              window.AdminMenuSpread.applyBeforeMenuInit();
            }
          </script>
        </aside>

        <div class="menu-mobile-toggler d-xl-none rounded-1">
          <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
            <i class="ti tabler-menu icon-base"></i>
            <i class="ti tabler-chevron-right icon-base"></i>
          </a>
        </div>
