<!doctype html>
<html
  lang="en"
  class="layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  style="overflow-x: hidden;"
  data-skin="default"
  data-bs-theme="light"
  data-assets-path="../../assets/"
  data-template="vertical-menu-template">
  <head>
    @include('admin.layouts.components.head')
  </head>

  <body class="pink">
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu (Sidebar) -->
        @include('admin.layouts.components.sidebar')

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar (Header) -->
          @include('admin.layouts.components.header')

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              @yield('content')
            </div>
            <!-- / Content -->

            <!-- Footer -->
            @include('admin.layouts.components.footer')
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    @stack('modals')

    <!-- Core JS & Scripts (đã gồm @stack('scripts') trong partial scripts) -->
    @include('admin.layouts.components.scripts')
  </body>
</html>
