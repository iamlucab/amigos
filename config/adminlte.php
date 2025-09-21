<?php

return [

    'title' => 'Amigos \'98',
    'title_prefix' => '',
    'title_postfix' => '',

    'use_ico_only' => false,
    'use_full_favicon' => false,

    'google_fonts' => [
        'allowed' => true,
    ],

    'logo' => '<b>Amigos</b> \'98',
    'logo_img' => 'images/logoround.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Amigos Logo',

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'images/logoround.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    'preloader' => [
        'enabled' => false,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'images/logoround.png',
            'alt' => 'Amigo Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => 'font-weight-bold',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-light elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-green navbar-dark',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    'right_sidebar' => false,
    'right_sidebar_icon' => 'bi bi-gear',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'logout_method' => 'post',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    'menu' => [
        ['header' => 'MAIN NAVIGATION'],
        ['text' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'bi bi-house'],

        [
            'text' => 'Members',
            'icon' => 'bi bi-people',
            'can'  => 'admin-only',
            'submenu' => [
                ['text' => 'All Members', 'url' => '/admin/members', 'icon' => 'bi bi-list'],
                ['text' => 'Pending', 'route' => 'admin.members.pending', 'icon' => 'bi bi-hourglass'],
                ['text' => 'Membership Codes', 'url' => 'admin/membership-codes', 'icon' => 'bi bi-qr-code'],
                ['text' => 'Code Request', 'route' => 'admin.membership-code-requests.index', 'icon' => 'bi bi-check-circle'],
            ],
        ],

        [
            'text' => 'Wallet Settings',
            'icon' => 'bi bi-wallet2',
            'can'  => 'admin-only',
            'submenu' => [
                ['text' => 'Cash In Approvals', 'url' => 'admin/cashin-approvals', 'icon' => 'bi bi-check-circle'],
                ['text' => 'Top-up / Refund', 'url' => 'admin/wallet/topup', 'icon' => 'bi bi-arrow-left-right'],
                ['text' => 'Loan Management', 'route' => 'admin.loans.management', 'icon' => 'bi bi-cash-coin'],
                ['text' => 'Loan Reports', 'route' => 'admin.loans.reports', 'icon' => 'bi bi-graph-up'],
            ],
        ],

        [
            'text' => 'E-commerce',
            'icon' => 'bi bi-shop',
            'can'  => 'admin-only',
            'submenu' => [
                ['text' => 'Manage Products', 'url' => 'admin/products', 'icon' => 'bi bi-box'],
                ['text' => 'Categories', 'url' => 'admin/product-categories', 'icon' => 'bi bi-tags'],
                // ['text' => 'Orders', 'url' => 'admin/orders', 'icon' => 'bi bi-cart'],
                ['text' => 'Order Reports', 'route' => 'admin.orders.index', 'icon' => 'bi bi-file-text'],
                ['text' => 'Referral Bonus', 'route' => 'referral.report', 'icon' => 'bi bi-gift'],
                ['text' => 'Top Earners', 'route' => 'admin.referral-bonuses', 'icon' => 'bi bi-coin'],
                ['text' => 'Settings', 'url' => 'admin/settings', 'icon' => 'bi bi-gear'],

               // ['text' => 'Cashback Programs', 'url' => 'admin/cashback-programs', 'icon' => 'fas fa-percent'],
               // ['text' => 'Cashback Transactions', 'url' => 'admin/cashback-transactions', 'icon' => 'fas fa-exchange-alt'],
               //['text' => 'Cashback Settings', 'url' => 'admin/cashback-settings', 'icon' => 'fas fa-cog'],

            ],
        ],

        // Staff-only menu for product management
        [
            'text' => 'Manage Products',
            'icon' => 'bi bi-box',
            'can'  => 'staff-only',
            'submenu' => [
                ['text' => 'All Products', 'url' => 'staff/products', 'icon' => 'bi bi-list'],
                ['text' => 'Add Product', 'url' => 'staff/products/create', 'icon' => 'bi bi-plus-circle'],
            ],
        ],

        [
            'text' => 'Rewards',
            'icon' => 'bi bi-gift',
            'can'  => 'admin-only',
            'submenu' => [
                ['text' => 'All Programs', 'url' => 'admin/rewards'],
                ['text' => 'Create Program', 'url' => 'admin/rewards/create'],
                ['text' => 'Winners', 'url' => 'admin/rewards/winners'],
            ],
        ],

        [
            'text' => 'Helpdesk',
            'icon' => 'bi bi-tools',
            'can'  => 'admin-only',
            'submenu' => [
                ['text' => 'All Tickets', 'url' => 'admin/tickets', 'icon' => 'bi bi-list'],
            ],
        ],

        [
            'text' => 'Broadcast Settings',
            'icon' => 'bi bi-broadcast',
            'can'  => 'admin-only',
            'submenu' => [
                ['text' => 'SMS', 'route' => 'admin.notifications.sms', 'icon' => 'bi bi-chat-text'],
                ['text' => 'Push Notification', 'route' => 'admin.notifications.push', 'icon' => 'bi bi-bell'],
            ],
        ],


        // ['text' => 'Staff Dashboard', 'url' => '/staff/dashboard', 'icon' => 'bi bi-people-fill', 'can' => 'staff-only'],

        [
            'text' => 'Loan Requests',
            'icon' => 'bi bi-cash-coin',
            'can'  => 'member-only',
            'submenu' => [
                ['text' => 'Loan History', 'url' => 'loans', 'icon' => 'bi bi-list-ul'],
            ],
        ],

        ['text' => 'Register Member', 'url' => 'member/register', 'icon' => 'bi bi-person-plus', 'can' => 'member-only'],
        ['text' => 'My Rewards', 'url' => 'member/rewards', 'icon' => 'bi bi-gift', 'can' => 'member-only'],
        ['text' => 'Helpdesk', 'url' => 'member/tickets', 'icon' => 'bi bi-headset', 'can' => 'member-only'],

        [
            'text' => 'Shopping',
            'icon' => 'bi bi-bag',
            'can'  => 'member-only',
            'submenu' => [
                ['text' => 'Products', 'route' => 'shop.index', 'icon' => 'bi bi-shop'],
                [
                    'text' => 'My Cart',
                    'route' => 'shop.cart',
                    'icon' => 'bi bi-basket',
                    'id' => 'cart-menu-item',
                ],
                ['text' => 'My Orders', 'route' => 'orders.index', 'icon' => 'bi bi-cart'],
            ],
        ],

        ['text' => 'Wallet', 'url' => '/wallet', 'icon' => 'bi bi-wallet2'],
        ['text' => 'My Network', 'url' => '/genealogy', 'icon' => 'bi bi-diagram-3', 'can' => 'member-only'],
        [
            'text' => 'Profile',
            'icon' => 'bi bi-person',
            'submenu' => [
                ['text' => 'My Profile', 'url' => '/profile', 'icon' => 'bi bi-person-circle'],
                [
                    'text' => 'Logout',
                    'url' => '#',
                    'icon' => 'bi bi-box-arrow-right',
                    'onclick' => 'event.preventDefault(); document.getElementById(\'logout-form\').submit();'
                ],
            ],
        ],
    ],

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    'plugins' => [
        'BootstrapIcons' => [
            'active' => true,
            'files' => [
                ['type' => 'css', 'asset' => false, 'location' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css'],
            ],
        ],
        'Datatables' => [
            'active' => true,
            'files' => [
                ['type' => 'js', 'asset' => false, 'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js'],
                ['type' => 'js', 'asset' => false, 'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js'],
                ['type' => 'css', 'asset' => false, 'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css'],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                ['type' => 'js', 'asset' => false, 'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js'],
                ['type' => 'css', 'asset' => false, 'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css'],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                ['type' => 'js', 'asset' => false, 'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js'],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                ['type' => 'js', 'asset' => false, 'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8'],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                ['type' => 'css', 'asset' => false, 'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css'],
                ['type' => 'js', 'asset' => false, 'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js'],
            ],
        ],
    ],

    'iframe' => [
        'default_tab' => ['url' => null, 'title' => null],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    'livewire' => false,

    /*
    |--------------------------------------------------------------------------
    | Custom JS
    |--------------------------------------------------------------------------
    */
    'js' => [

    ],
];
