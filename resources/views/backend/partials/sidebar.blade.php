<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{route('backend.dashboard.index')}}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{$settings->mini_logo ? asset('storage/' . $settings->mini_logo) : asset('assets/images/logo-sm.png')}}"
                    alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{'storage/' . $settings->logo ? asset('storage/' . $settings->logo) : asset('assets/images/logo-dark.png')}}"
                    alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{route('backend.dashboard.index')}}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{'storage/' . $settings->mini_logo ? asset('storage/' . $settings->mini_logo) : asset('assets/images/logo-sm.png')}}"
                    alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{'storage/' . $settings->logo ? asset('storage/' . $settings->logo) : asset('assets/images/logo-light.png')}}"
                    alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
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
                    <a class="nav-link menu-link  {{getPageStatus('backend.dashboard.*', 'collapsed active')}}"
                        href="{{ route('backend.dashboard.index') }}" role="button">
                       <i class="ri-home-line"></i>

                        <span data-key="t-chats">Home</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link  {{getPageStatus('backend.chats.*', 'collapsed active')}}"
                        href="{{ route('backend.chats.index') }}" role="button">
                       <i class="ri-chat-smile-2-line"></i>

                        <span data-key="t-chats">Chats</span>
                    </a>
                </li>
                <!-- end Dashboard Menu -->
                <li class="nav-item">
                    {{-- <a class="nav-link menu-link {{getPageStatus('backend.feature.*', 'collapsed active')}}" href="#sidebarApps" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarApps">  --}}
                    <a class="nav-link menu-link {{getPageStatus('backend.feature.*', 'collapsed active')}}"
                        href="#sidebarApps" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="sidebarApps">
                        <i class="ri-apps-2-line"></i> <span data-key="t-apps">Features</span>
                    </a>
                    <div class="collapse menu-dropdown {{getPageStatus('backend.feature.*', 'show')}}" id="sidebarApps">

                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a 
                                    {{-- href="{{ route('backend.feature.listings.index') }}"  --}}
                                    class="nav-link {{getPageStatus('backend.feature.listings.*', 'active')}}" role="button" aria-expanded="false"
                                    aria-controls="sidebarProjects" data-key="t-projects">
                                    Listings
                                </a>
                            </li> 
                            
                            <li class="nav-item">
                                <a href="#sidebarAgeGroups"
                                    class="nav-link {{ getPageStatus('backend.feature.age_groups.*', 'active') }}"
                                    data-bs-toggle="collapse" role="button" aria-expanded="false"
                                    aria-controls="sidebarAgeGroups" data-key="t-age-groups">
                                    Categories By Age Group
                                </a>
                                <div class="collapse menu-dropdown {{ getPageStatus('backend.feature.age_groups.*', 'show') }}"
                                    id="sidebarAgeGroups">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a 
                                                {{-- href="{{ route('backend.feature.age_groups.index') }}" --}}
                                                class="nav-link {{ getPageStatus('backend.feature.age_groups.*') }}"
                                                data-key="t-list-view">
                                                List View
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a 
                                {{-- href="{{ route('backend.feature.taxes.index') }}"  --}}
                                class="nav-link {{getPageStatus('backend.feature.taxes.*', 'active')}}" role="button"
                                    aria-expanded="false" aria-controls="sidebarTaxes" data-key="t-taxes">
                                    Taxes
                                </a>

                            </li>
                        </ul>
                    </div>
                </li>
                
                <li class="nav-item">
                    <a href="#sidebarBookings"
                        class="nav-link {{ getPageStatus('backend.booking.*', 'active') }}"
                        data-bs-toggle="collapse" role="button" aria-expanded="false"aria-controls="sidebarBookings" >
                        <i class="ri-bookmark-2-line fs-4"></i><span data-key="t-advertisements">Booking & Transactions</span>
                        
                    </a>
                    <div class="collapse menu-dropdown {{ getPageStatus('backend.booking.*', 'show') }}"
                        id="sidebarBookings">
                        <ul class="nav nav-sm flex-column">
                            {{-- <li class="nav-item">
                                <a href="{{ route('backend.booking.booking-lookup.index') }}"
                                    class="nav-link {{ getPageStatus('backend.booking.booking-lookup.*') }}"
                                    data-key="t-lookup-view">
                                    Lookup
                                </a>
                            </li> --}}
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="#sidebarAdvertisements"
                        class="nav-link {{ getPageStatus('backend.web-content.*', 'active') }}"
                        data-bs-toggle="collapse" role="button" aria-expanded="false"aria-controls="sidebarAdvertisements" >
                        <i class="ri-advertisement-line fs-4"></i><span data-key="t-advertisements">Web Content</span>
                        
                    </a>
                    <div class="collapse menu-dropdown {{ getPageStatus('backend.web-content.*', 'show') }}"
                        id="sidebarAdvertisements">
                        {{-- <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('backend.web-content.advertisements.index') }}"
                                    class="nav-link {{ getPageStatus('backend.web-content.advertisements.*') }}"
                                    data-key="t-add-view">
                                    Advertisements
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('backend.web-content.add-reqs.index') }}"
                                    class="nav-link {{ getPageStatus('backend.web-content.add-reqs.*') }}"
                                    data-key="t-add-view">
                                    Requests
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('backend.web-content.advertisements.advert.index') }}"
                                    class="nav-link {{ getPageStatus('backend.web-content.advertisements.advert.*') }}"
                                    data-key="t-add-view">
                                    Subscription Plans
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('backend.web-content.faq.index') }}"
                                    class="nav-link {{ getPageStatus('backend.web-content.faq.*') }}"
                                    data-key="t-faq-view">
                                    FAQ
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('backend.web-content.social-links.index') }}"
                                    class="nav-link {{ getPageStatus('backend.web-content.social-links.*') }}"
                                    data-key="t-faq-view">
                                    Social Links
                                </a>
                            </li>
                        </ul> --}}
                    </div>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link menu-link  {{getPageStatus('backend.blog.*', 'collapsed active')}}"
                        {{-- href="{{ route('backend.blog.index') }}"  --}}
                        role="button">
                       <i class="ri-chat-smile-2-line"></i>

                        <span data-key="t-blog">Blog</span>
                    </a>
                </li>
                
                <ul class="navbar-nav" id="navbar-nav">

                    {{-- ... Continue with other sidebar entries ... --}}

                </ul>

                {{-- </li> --}}

                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Pages</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link  {{getPageStatus('backend.page.*', 'collapsed active')}}"
                        href="{{ route('backend.page.index') }}" role="button">
                       <i class="ri-chat-smile-2-line"></i>

                        <span data-key="t-page">Pages</span>
                    </a>
                </li>

                
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                <li class="nav-item">
                    <a href="#sidebarUsers"
                        class="nav-link {{ getPageStatus('backend.system-user.*', 'active') }}"
                        data-bs-toggle="collapse" role="button" aria-expanded="false"aria-controls="sidebarUsers" >
                        <i class="ri-dashboard-line fs-4"></i><span data-key="t-users">Users</span>
                        
                    </a>
                    <div class="collapse menu-dropdown {{ getPageStatus('backend.system-user.*', 'show') }}"
                        id="sidebarUsers">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('backend.system-user.index') }}"
                                    class="nav-link {{ getPageStatus('backend.system-user.*') }}"
                                    data-key="t-add-view">
                                    System Users
                                </a>
                            </li>
                            {{-- <li class="nav-item">
                                <a href="{{ route('backend.system-user.customer.index') }}"
                                    class="nav-link {{ getPageStatus('backend.system-user.customer.*') }}"
                                    data-key="t-add-view">
                                    Customers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('backend.system-user.vendor.index') }}"
                                    class="nav-link {{ getPageStatus('backend.system-user.vendor.*') }}"
                                    data-key="t-add-view">
                                    Vendors
                                </a>
                            </li> --}}
                             
                        </ul>
                    </div>
                </li>
                


                <li class="nav-item">
                    <a class="nav-link menu-link {{getPageStatus('backend.settings.*')}}" href="#sidebarMultilevel"
                        data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarMultilevel">
                        <i class="ri-share-line"></i> <span data-key="t-multi-level">Settings</span>
                    </a>
                    <div class="collapse menu-dropdown {{getPageStatus('backend.settings.*', 'show')}}"
                        id="sidebarMultilevel">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{route('backend.settings.profile.index')}}"
                                    class="nav-link {{getPageStatus('backend.settings.profile.*')}}"
                                    data-key="t-level-1.1"> Profile Settings </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('backend.settings.system.index')}}"
                                    class="nav-link {{getPageStatus('backend.settings.system.*')}}"
                                    data-key="t-level-1.1"> System Settings </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('backend.settings.mail.index')}}"
                                    class="nav-link {{getPageStatus('backend.settings.mail.*')}}"
                                    data-key="t-level-1.1"> Mail Settings</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('backend.settings.payments.stripe.index')}}"
                                    class="nav-link {{getPageStatus('backend.settings.payments.*')}}"
                                    data-key="t-level-1.1"> Payment Settings</a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('backend.settings.get.maintainace.page')}}"
                                    class="nav-link {{getPageStatus('backend.settings.get.maintainace.page')}}"
                                    data-key="t-level-1.1"> maintainance</a>
                            </li>
                            
                            <li class="nav-item">
                                <a href="{{route('backend.settings.get.clear-cache.page')}}"
                                    class="nav-link {{getPageStatus('backend.settings.get.clear-cache.page')}}"
                                    data-key="t-level-1.1"> Clear Cache</a>
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