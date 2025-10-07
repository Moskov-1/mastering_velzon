<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index.html" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{asset('')}}assets/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{asset('')}}assets/images/logo-dark.png" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index.html" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{asset('')}}assets/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{asset('')}}assets/images/logo-light.png" alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                        <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboards</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarDashboards">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="index.html" class="nav-link" data-key="t-ecommerce"> Home </a>
                            </li>
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarApps" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarApps">
                        <i class="ri-apps-2-line"></i> <span data-key="t-apps">Features</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarApps">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="#sidebarProjects" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProjects" data-key="t-projects">
                                    Projects
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarProjects">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="#" class="nav-link" data-key="t-list"> List
                                            {{-- <a href="{{route('backend.project.index')}}" class="nav-link" data-key="t-list"> List --}}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            {{-- <a href="{{route('backend.project.create')}}" class="nav-link" data-key="t-create-project"> Create Project </a> --}}
                                            <a href="#" class="nav-link" data-key="t-create-project"> Create Project </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="#sidebarTasks" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTasks" 
                                data-key="t-tasks"> 
                                    FAQ
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarTasks">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{route('backend.faq.index')}}" class="nav-link" data-key="t-list-view">
                                                List View 
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{route('backend.faq.create')}}" class="nav-link" data-key="t-task-details"> Create FAQ </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Pages</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarAuth" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAuth">
                        <i class="ri-account-circle-line"></i> <span data-key="t-authentication">Authentication</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarAuth">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="#sidebarSignIn" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSignIn" data-key="t-signin"> Sign In
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarSignIn">
                                    <ul class="nav nav-sm flex-column">
                                        
                                        <li class="nav-item">
                                            <a href="auth-signin-cover.html" class="nav-link" data-key="t-cover"> Cover
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="#sidebarSignUp" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSignUp" data-key="t-signup"> Sign Up
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarSignUp">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="auth-signup-cover.html" class="nav-link" data-key="t-cover"> Cover
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a href="#sidebarResetPass" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarResetPass" data-key="t-password-reset">
                                    Password Reset
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarResetPass">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="auth-pass-reset-basic.html" class="nav-link" data-key="t-basic">
                                                Basic </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="auth-pass-reset-cover.html" class="nav-link" data-key="t-cover">
                                                Cover </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a href="#sidebarchangePass" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarchangePass" data-key="t-password-create">
                                    Password Create
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarchangePass">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="auth-pass-change-basic.html" class="nav-link" data-key="t-basic">
                                                Basic </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="auth-pass-change-cover.html" class="nav-link" data-key="t-cover">
                                                Cover </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a href="#sidebarLogout" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLogout" 
                                data-key="t-logout"> Logout
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarLogout">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="auth-logout-basic.html" class="nav-link" data-key="t-basic"> Basic
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="auth-logout-cover.html" class="nav-link" data-key="t-cover"> Cover
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a href="#sidebarSuccessMsg" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSuccessMsg" 
                                data-key="t-success-message"> Success Message
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarSuccessMsg">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="auth-success-msg-basic.html" class="nav-link" data-key="t-basic">
                                                Basic </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="auth-success-msg-cover.html" class="nav-link" data-key="t-cover">
                                                Cover </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="#sidebarTwoStep" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTwoStep" 
                                data-key="t-two-step-verification"> Two Step Verification
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarTwoStep">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="auth-twostep-basic.html" class="nav-link" data-key="t-basic"> Basic
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="auth-twostep-cover.html" class="nav-link" data-key="t-cover"> Cover
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="#sidebarErrors" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarErrors" data-key="t-errors"> Errors
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarErrors">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="auth-404-basic.html" class="nav-link" data-key="t-404-basic"> 404
                                                Basic </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="auth-404-cover.html" class="nav-link" data-key="t-404-cover"> 404
                                                Cover </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="auth-404-alt.html" class="nav-link" data-key="t-404-alt"> 404 Alt
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="auth-500.html" class="nav-link" data-key="t-500"> 500 </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="auth-offline.html" class="nav-link" data-key="t-offline-page"> Offline Page </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarPages" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarPages">
                        <i class="ri-pages-line"></i> <span data-key="t-pages">Pages</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarPages">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="pages-starter.html" class="nav-link" data-key="t-starter"> Starter </a>
                            </li>
                            <li class="nav-item">
                                <a href="#sidebarProfile" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProfile" data-key="t-profile"> Profile
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarProfile">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="pages-profile.html" class="nav-link" data-key="t-simple-page">
                                                Simple Page </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="pages-profile-settings.html" class="nav-link" data-key="t-settings"> Settings </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="pages-team.html" class="nav-link" data-key="t-team"> Team </a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-timeline.html" class="nav-link" data-key="t-timeline"> Timeline </a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-faqs.html" class="nav-link" data-key="t-faqs"> FAQs </a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-pricing.html" class="nav-link" data-key="t-pricing"> Pricing </a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-gallery.html" class="nav-link" data-key="t-gallery"> Gallery </a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-maintenance.html" class="nav-link" data-key="t-maintenance"> Maintenance
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-coming-soon.html" class="nav-link" data-key="t-coming-soon"> Coming Soon
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-sitemap.html" class="nav-link" data-key="t-sitemap"> Sitemap </a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-search-results.html" class="nav-link" data-key="t-search-results"> Search
                                    Results </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarMultilevel" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarMultilevel">
                        <i class="ri-share-line"></i> <span data-key="t-multi-level">Settings</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarMultilevel">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{route('backend.settings.profile.index')}}" class="nav-link" data-key="t-level-1.1"> Profile Settings </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-level-1.1"> System Settings </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-level-1.1"> Admin Settings </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-level-1.1"> Mail Settings </a>
                            </li>
                            <li class="nav-item">
                                <a href="#sidebarAccount" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAccount" data-key="t-level-1.2"> Level
                                    1.2
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarAccount">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="#" class="nav-link" data-key="t-level-2.1"> Level 2.1 </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#sidebarCrm" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCrm" data-key="t-level-2.2"> Level 2.2
                                            </a>
                                            <div class="collapse menu-dropdown" id="sidebarCrm">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="#" class="nav-link" data-key="t-level-3.1"> Level 3.1
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="#" class="nav-link" data-key="t-level-3.2"> Level 3.2
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>