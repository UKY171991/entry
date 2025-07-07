<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand px-4 py-3 m-0" href="dashboard.php">
        <img src="assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26" alt="main_logo">
        <span class="ms-1 text-sm text-dark">Creative Tim</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link <?php if($current_page == 'dashboard.php') echo 'active bg-gradient-dark text-white'; else echo 'text-dark'; ?>" href="dashboard.php">
            <i class="material-symbols-rounded opacity-5">dashboard</i>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if($current_page == 'add-client.php' || $current_page == 'client-list.php') echo 'active bg-gradient-dark text-white'; else echo 'text-dark'; ?>" href="add-client.php">
            <i class="material-symbols-rounded opacity-5">groups</i>
            <span class="nav-link-text ms-1">Client</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if($current_page == 'income.php') echo 'active bg-gradient-dark text-white'; else echo 'text-dark'; ?>" href="income.php">
            <i class="material-symbols-rounded opacity-5">table_view</i>
            <span class="nav-link-text ms-1">Income</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if($current_page == 'expense.php') echo 'active bg-gradient-dark text-white'; else echo 'text-dark'; ?>" href="expense.php">
            <i class="material-symbols-rounded opacity-5">receipt_long</i>
            <span class="nav-link-text ms-1">Expense</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link <?php if($current_page == 'email.php') echo 'active bg-gradient-dark text-white'; else echo 'text-dark'; ?>" href="email.php">
            <i class="material-symbols-rounded opacity-5">receipt_long</i>
            <span class="nav-link-text ms-1">Email</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link <?php if($current_page == 'websites.php') echo 'active bg-gradient-dark text-white'; else echo 'text-dark'; ?>" href="websites.php">
            <i class="material-symbols-rounded opacity-5">receipt_long</i>
            <span class="nav-link-text ms-1">websites</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link <?php if($current_page == 'pending-task.php') echo 'active bg-gradient-dark text-white'; else echo 'text-dark'; ?>" href="pending-task.php">
            <i class="material-symbols-rounded opacity-5">task</i>
            <span class="nav-link-text ms-1">Pending Task</span>
          </a>
        </li>
        
        
        
        <!--<li class="nav-item mt-3">-->
        <!--  <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">Account pages</h6>-->
        <!--</li>-->
        <!--<li class="nav-item">-->
        <!--  <a class="nav-link text-dark" href="profile.html">-->
        <!--    <i class="material-symbols-rounded opacity-5">person</i>-->
        <!--    <span class="nav-link-text ms-1">Profile</span>-->
        <!--  </a>-->
        <!--</li>-->
        <!--<li class="nav-item">-->
        <!--  <a class="nav-link text-dark" href="sign-in.html">-->
        <!--    <i class="material-symbols-rounded opacity-5">login</i>-->
        <!--    <span class="nav-link-text ms-1">Sign In</span>-->
        <!--  </a>-->
        <!--</li>-->
        <!--<li class="nav-item">-->
        <!--  <a class="nav-link text-dark" href="sign-up.html">-->
        <!--    <i class="material-symbols-rounded opacity-5">assignment</i>-->
        <!--    <span class="nav-link-text ms-1">Sign Up</span>-->
        <!--  </a>-->
        <!--</li>-->
        
        
      </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0 ">
      <div class="mx-3">
        <a class="btn bg-gradient-dark w-100" href="logout.php" type="button">Logout</a>
      </div>
    </div>
  </aside>