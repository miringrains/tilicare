
<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link" href="{{ asset('admin_panel') }}">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li><!-- End Dashboard Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#checkout-tools-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-menu-button-wide"></i><span>Checkout Tools</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="checkout-tools-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{ asset('admin_panel') }}">
            <i class="bi bi-circle"></i><span>Invoices</span>
          </a>
        </li>
        <li>
          <a href="{{ asset('admin_panel') }}">
            <i class="bi bi-circle"></i><span>Overview</span>
          </a>
        </li>
      </ul>
    </li><!-- End Components Nav -->


    <li class="nav-item">
      <a class="nav-link collapsed" href="{{ asset('admin_panel') }}">
        <i class="bi bi-grid"></i>
        <span>Appointments</span>
      </a>
    </li><!-- End Appointments Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="{{ asset('admin_panel') }}">
        <i class="bi bi-grid"></i>
        <span>Customers</span>
      </a>
    </li><!-- End Appointments Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="{{ asset('admin_panel') }}">
        <i class="bi bi-grid"></i>
        <span>Gift Cards</span>
      </a>
    </li><!-- End Appointments Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="{{ asset('admin_panel') }}">
        <i class="bi bi-grid"></i>
        <span>Package Prices</span>
      </a>
    </li><!-- End Appointments Nav -->


    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#tili-team-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-layout-text-window-reverse"></i><span>TiliTeam</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="tili-team-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{ asset('admin_panel') }}">
            <i class="bi bi-circle"></i><span>Detailer Activation</span>
          </a>
        </li>
        <li>
          <a href="{{ asset('admin_panel') }}">
            <i class="bi bi-circle"></i><span>Detailer Data</span>
          </a>
        </li>
        <li>
          <a href="{{ asset('admin_panel') }}">
            <i class="bi bi-circle"></i><span>Driver's Status</span>
          </a>
        </li>
        <li>
          <a href="{{ asset('admin_panel') }}">
            <i class="bi bi-history"></i><span>Job History</span>
          </a>
        </li>
        <li>
          <a href="{{ asset('admin_panel') }}">
            <i class="bi bi-zip"></i><span>Zip Codes</span>
          </a>
        </li>
      </ul>
    </li><!-- End Tables Nav -->
    
    <li class="nav-heading">Account</li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="{{ asset('admin_panel') }}">
        <i class="bi bi-envelope"></i>
        <span>My Profile</span>
      </a>
    </li><!-- End Contact Page Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="{{ asset('admin_panel') }}">
        <i class="bi bi-card-list"></i>
        <span>Logout</span>
      </a>
    </li><!-- End Register Page Nav -->
  </ul>
</aside><!-- End Sidebar-->