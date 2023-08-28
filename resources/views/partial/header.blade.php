@php
    use App\Helpers\App;
@endphp

<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
      <ul class="navbar-nav mr-3">
        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
      </ul>
    </form>
    <ul class="navbar-nav navbar-right">
      <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
        <img alt="image" src="{{asset('assets/img/avatar/avatar-1.png')}}" class="rounded-circle mr-1">
        <div class="d-sm-none d-lg-inline-block">Hi, {{ Auth::user()->nama }}</div></a>
        <div class="dropdown-menu dropdown-menu-right">
          <a href="/change-password" class="dropdown-item has-icon">
            <i class="far fa-user"></i> Change Password
          </a>
          <div class="dropdown-divider"></div>
          <a href="/logout" class="dropdown-item has-icon text-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
        </div>
      </li>
    </ul>
  </nav>
  <div class="main-sidebar">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="/admin/dashboard">SALES KS</a>
      </div>
      <div class="sidebar-brand sidebar-brand-sm">
        <a href="/admin/dashboard">KS</a>
      </div>
        <?php
          $url_menu = Request::segment(2);
          $url_submenu = Request::segment(3);
        ?>
        <ul class="sidebar-menu">
          <li class="menu-header">Dashboard</li>
          <li class="nav-item dropdown @if($url_menu == "dashboard") active @endif"><a href="/admin/dashboard" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a></li>

          @if(Auth::user()->access_menu == 0 || !empty(array_intersect([1,2,3,4], Auth::user()->access_menu)))
          <li class="menu-header">Data Master</li>
          @if (Auth::user()->access_menu == 0 || in_array(4, Auth::user()->access_menu))
          <li class="nav-item dropdown @if($url_menu == "user") active @endif"><a href="/admin/user" class="nav-link"><i class="fas fa-user-lock"></i><span>Master Administrator</span></a></li>
          @endif
          @if (Auth::user()->access_menu == 0 || in_array(1, Auth::user()->access_menu))
          <li class="nav-item dropdown @if($url_menu == "sales") active @endif"><a href="/admin/sales" class="nav-link"><i class="fas fa-user-cog"></i><span>Master Sales</span></a></li>
          @endif
          @if (Auth::user()->access_menu == 0 || in_array(2, Auth::user()->access_menu))
          <li class="nav-item dropdown @if($url_menu == "pelanggan") active @endif"><a href="/admin/pelanggan" class="nav-link"><i class="fas fa-user"></i><span>Master Pelanggan</span></a></li>
          @endif
          @if (Auth::user()->access_menu == 0 || in_array(3, Auth::user()->access_menu))
          <li class="nav-item dropdown @if($url_menu == "produk") active @endif"><a href="/admin/produk" class="nav-link"><i class="fas fa-shopping-cart"></i><span>Master Produk</span></a></li>
          @endif
          @endif

          @if(Auth::user()->access_menu == 0 || !empty(array_intersect([5,6], Auth::user()->access_menu)))
          <li class="menu-header">Master Sales</li>
          @if (Auth::user()->access_menu == 0 || in_array(5, Auth::user()->access_menu))
          <li class="nav-item dropdown @if($url_menu == "edit-absensi-sales") active @endif"><a href="/admin/edit-absensi-sales" class="nav-link"><i class="fas fa-user-edit"></i><span>Edit Absensi Sales</span></a></li>
          @endif
          @if (Auth::user()->access_menu == 0 || in_array(6, Auth::user()->access_menu))
          <li class="nav-item dropdown @if($url_menu == "edit-kunjungan-sales") active @endif"><a href="/admin/edit-kunjungan-sales" class="nav-link"><i class="fas fa-street-view"></i><span>Edit Kunjungan Sales</span></a></li>
          @endif
          @endif

          @if(Auth::user()->access_menu == 0 || !empty(array_intersect([7,8,9], Auth::user()->access_menu)))
          <li class="menu-header">Report Sales</li>
          @if (Auth::user()->access_menu == 0 || in_array(7, Auth::user()->access_menu))
          <li class="nav-item dropdown @if($url_menu == "absensi-sales" && !isset($url_submenu)) active @endif"><a href="/admin/absensi-sales" class="nav-link"><i class="fas fa-user-clock"></i><span>Absensi Sales</span></a></li>
          @endif
          @if (Auth::user()->access_menu == 0 || in_array(8, Auth::user()->access_menu))
          <li class="nav-item dropdown @if($url_menu == "kunjungan-sales" && !isset($url_submenu)) active @endif"><a href="/admin/kunjungan-sales" class="nav-link"><i class="fas fa-running"></i><span>Kunjungan Sales</span></a></li>
          @endif
          @if (Auth::user()->access_menu == 0 || in_array(9, Auth::user()->access_menu))
          <li class="nav-item dropdown @if($url_menu == "sales-log") active @endif"><a href="/admin/sales-log" class="nav-link"><i class="fas fa-map"></i><span>Log Sales</span></a></li>
          @endif
          @endif

          @if(Auth::user()->access_menu == 0 || !empty(array_intersect([10,11], Auth::user()->access_menu)))
          <li class="menu-header">Apps</li>
          {{-- <li class="nav-item dropdown @if($url_menu == "kode-voucher") active @endif"><a href="/admin/kode-voucher" class="nav-link"><i class="fas fa-tag"></i><span>Kode Voucher</span></a></li> --}}
          @if (Auth::user()->access_menu == 0 || in_array(10, Auth::user()->access_menu))
          <li class="nav-item dropdown @if($url_menu == "transaksi") active @endif"><a href="/admin/transaksi" class="nav-link"><i class="fas fa-shopping-cart"></i><span>Transaksi</span></a></li>
          @endif
          @if (Auth::user()->access_menu == 0 || in_array(11, Auth::user()->access_menu))
          <li class="nav-item dropdown @if($url_menu == "jasa-pengiriman") active @endif"><a href="/admin/jasa-pengiriman" class="nav-link"><i class="fas fa-truck"></i><span>Jasa Pengiriman</span></a></li>
          @endif
          @if (Auth::user()->access_menu == 0 || in_array(12, Auth::user()->access_menu))
          <li class="nav-item dropdown @if($url_menu == "config") active @endif"><a href="/admin/config" class="nav-link"><i class="fas fa-cogs"></i><span>Config</span></a></li>
          @endif
          @endif
        </ul>

    </aside>
  </div>
