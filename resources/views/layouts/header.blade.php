<div class="az-header">
    <div class="container">
        <div class="az-header-left">
            <a href="{{ route('home') }}" class="az-logo text-uppercase"><span></span> BES</a>
            <a href="" id="azMenuShow" class="az-header-menu-icon d-lg-none"><span></span></a>
        </div><!-- az-header-left -->
        <div class="az-header-menu">
            <div class="az-header-menu-header">
                <a href="{{ route('home') }}" class="az-logo text-uppercase"><span></span> BES</a>
                <a href="" class="close">&times;</a>
            </div><!-- az-header-menu-header -->
            <ul class="nav">
                <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <a href="{{ route('home') }}" class="nav-link"><i class="typcn typcn-chart-area-outline"></i>
                        Dashboard</a>
                </li>
                <li
                    class="nav-item {{ request()->routeIs('one.time.sender') || request()->routeIs('saved.sender') ? 'active' : '' }}">
                    <a href="" class="nav-link with-sub"><i class="typcn typcn-document"></i> Email Sender</a>
                    <nav class="az-menu-sub">
                        <a href="{{ route('one.time.sender') }}" class="nav-link">One Time Sender</a>
                        <a href="{{ route('saved.sender') }}" class="nav-link">Saved Sender</a>
                    </nav>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0);" onclick="alert('This feature is currently under construction. Please check back later.'); return false;" class="nav-link">
                        <i class="typcn typcn-chart-bar-outline"></i> Configuration
                    </a>
                    
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0);" onclick="alert('This feature is currently under construction. Please check back later.'); return false;" class="nav-link">
                        <i class="typcn typcn-user-outline"></i> Profile
                    </a>
                </li>
            </ul>
        </div><!-- az-header-menu -->
        <div class="az-header-right">
            <div class="dropdown az-profile-menu">
                <a href="" class="az-img-user"><img src="{{ asset('assets/img/faces/face1.jpg') }}"
                        alt=""></a>
                <div class="dropdown-menu">
                    <div class="az-dropdown-header d-sm-none">
                        <a href="" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
                    </div>
                    <div class="az-header-profile">
                        <div class="az-img-user">
                            <img src="{{ asset('assets/img/faces/face1.jpg') }}" alt="">
                        </div><!-- az-img-user -->
                        <h6>{{auth()->user()->name }}</h6>
                        <span>Admin</span>
                    </div><!-- az-header-profile -->

                    <a href="javascript:void(0);" onclick="alert('This feature is currently under construction. Please check back later.'); return false;" class="dropdown-item"><i class="typcn typcn-user-outline"></i> My Profile</a>
                    <a href="javascript:void(0);" onclick="alert('This feature is currently under construction. Please check back later.'); return false;" class="dropdown-item"><i class="typcn typcn-edit"></i> Edit Profile</a>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="dropdown-item"><i class="typcn typcn-power-outline"></i>
                            LogOut
                        </button>
                    </form>

                </div><!-- dropdown-menu -->
            </div>
        </div><!-- az-header-right -->
    </div><!-- container -->
</div><!-- az-header -->
