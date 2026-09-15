<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AirSen — İdarəetmə Paneli</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Google Fonts (Plus Jakarta Sans & Inter with Full Latin Extended Azerbaijani ə, ı, ö, ğ, ç, ş support) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ time() }}">
    
    <!-- Chart.js & SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        if (window.Swal && typeof window.Swal.mixin === 'function') {
            window.Swal = window.Swal.mixin({
                confirmButtonText: 'Bağla',
                cancelButtonText: 'Ləğv et',
                denyButtonText: 'Xeyr'
            });
        }
    </script>

    <!-- Chart Data from Server -->
    <script>
        window.adminChartLabels = {!! json_encode($chartLabels) !!};
        window.adminChartPageviews = {!! json_encode($chartPageviews) !!};
        window.adminChartVisitors = {!! json_encode($chartVisitors) !!};
        window.adminDeviceLabels = {!! json_encode($deviceStats->pluck('device_name')) !!};
        window.adminDeviceCounts = {!! json_encode($deviceStats->pluck('total')) !!};
    </script>

    <!-- Embedded AirSen Modal & Core Navigation Engine -->
    <script>
        window.tabTitles = {
            'overview': 'Əsas Səhifə',
            'charts': 'Ziyarət Qrafikləri',
            'reviews': 'Rəylərin İdarə Edilməsi',
            'traffic': 'Canlı Ziyarətçi Logları',
            'beta': 'Beta Abunəçiləri',
            'team': 'Admin & İcazələr',
            'settings': 'Google Play & Ayarlar'
        };

        // Fallback Swal Polyfill in case CDN is blocked, slow or offline
        if (!window.Swal) {
            window.Swal = {
                fire: function(opts) {
                    return new Promise(function(resolve) {
                        if (typeof opts === 'string') opts = { title: opts };
                        opts = opts || {};

                        const oldModal = document.getElementById('airsen-fallback-swal-modal');
                        if (oldModal) oldModal.remove();

                        const overlay = document.createElement('div');
                        overlay.id = 'airsen-fallback-swal-modal';
                        overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(5,5,8,0.78); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 999999; padding: 20px; box-sizing: border-box;';

                        const iconHtml = opts.icon === 'warning' ? '<div style="font-size: 38px; margin-bottom: 12px; color: #F59E0B;">⚠️</div>'
                            : opts.icon === 'success' ? '<div style="font-size: 38px; margin-bottom: 12px; color: #10B981;">✅</div>'
                            : opts.icon === 'error' ? '<div style="font-size: 38px; margin-bottom: 12px; color: #EF4444;">❌</div>'
                            : opts.icon === 'question' ? '<div style="font-size: 38px; margin-bottom: 12px; color: #8B5CF6;">❓</div>'
                            : opts.icon === 'info' ? '<div style="font-size: 38px; margin-bottom: 12px; color: #3B82F6;">ℹ️</div>' : '';

                        const modal = document.createElement('div');
                        modal.style.cssText = `background: ${opts.background || '#16161C'}; color: ${opts.color || '#FFFFFF'}; border-radius: ${opts.borderRadius || '20px'}; border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.8); max-width: 480px; width: 100%; padding: 28px 24px; text-align: center; box-sizing: border-box; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; position: relative;`;

                        let contentHtml = iconHtml;
                        if (opts.title) {
                            contentHtml += `<h3 style="font-size: 19px; font-weight: 700; margin: 0 0 10px; color: #FFF; line-height: 1.4;">${opts.title}</h3>`;
                        }
                        if (opts.text) {
                            contentHtml += `<p style="font-size: 14px; color: #9CA3AF; margin: 0 0 20px; line-height: 1.5;">${opts.text}</p>`;
                        }
                        if (opts.html) {
                            contentHtml += `<div id="swalFallbackCustomHtml" style="margin-bottom: 20px; font-size: 14px; color: #D1D5DB;">${opts.html}</div>`;
                        }

                        contentHtml += `<div id="swalFallbackValidationMsg" style="display: none; color: #EF4444; font-size: 13px; margin-bottom: 14px; font-weight: 600;"></div>`;

                        const showCancel = opts.showCancelButton === true;
                        const showConfirm = opts.showConfirmButton !== false;

                        contentHtml += `<div style="display: flex; gap: 12px; justify-content: center; align-items: center; margin-top: 20px;">`;
                        if (showCancel) {
                            contentHtml += `<button type="button" id="swalFallbackCancelBtn" style="padding: 11px 20px; background: ${opts.cancelButtonColor || '#24242F'}; color: #9CA3AF; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s;">${opts.cancelButtonText || 'Ləğv et'}</button>`;
                        }
                        if (showConfirm) {
                            contentHtml += `<button type="button" id="swalFallbackConfirmBtn" style="padding: 11px 24px; background: ${opts.confirmButtonColor || '#C042F0'}; color: #FFF; border: none; border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer; box-shadow: 0 4px 14px rgba(192,66,240,0.3); transition: all 0.2s;">${opts.confirmButtonText || 'Təsdiqlə'}</button>`;
                        }
                        contentHtml += `</div>`;

                        modal.innerHTML = contentHtml;
                        overlay.appendChild(modal);
                        document.body.appendChild(overlay);

                        if (typeof opts.didOpen === 'function') {
                            try { opts.didOpen(); } catch(e) {}
                        }

                        if (opts.timer && typeof opts.timer === 'number') {
                            setTimeout(function() {
                                overlay.remove();
                                resolve({ isConfirmed: false, isDismissed: true });
                            }, opts.timer);
                        }

                        const confirmBtn = document.getElementById('swalFallbackConfirmBtn');
                        const cancelBtn = document.getElementById('swalFallbackCancelBtn');

                        if (cancelBtn) {
                            cancelBtn.onclick = function() {
                                overlay.remove();
                                resolve({ isConfirmed: false, isDismissed: true });
                            };
                        }

                        if (confirmBtn) {
                            confirmBtn.onclick = function() {
                                if (typeof opts.preConfirm === 'function') {
                                    const res = opts.preConfirm();
                                    if (res === false) return;
                                }
                                overlay.remove();
                                resolve({ isConfirmed: true, value: true });
                            };
                        }
                    });
                },
                showValidationMessage: function(msg) {
                    const el = document.getElementById('swalFallbackValidationMsg');
                    if (el) {
                        el.textContent = msg;
                        el.style.display = 'block';
                    }
                },
                close: function() {
                    const oldModal = document.getElementById('airsen-fallback-swal-modal');
                    if (oldModal) oldModal.remove();
                }
            };
        }

        window.toggleMobileSidebar = function() {
            const sidebar = document.getElementById('dashboardSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) {
                const isActive = sidebar.classList.toggle('show');
                if (backdrop) backdrop.classList.toggle('show', isActive);
                document.body.classList.toggle('sidebar-locked', isActive);
            }
        };

        window.closeMobileSidebar = function() {
            const sidebar = document.getElementById('dashboardSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.remove('show');
            if (backdrop) backdrop.classList.remove('show');
            document.body.classList.remove('sidebar-locked');
        };

        window.detectInitialTab = function() {
            const validTabs = ['overview', 'charts', 'reviews', 'traffic', 'beta', 'team', 'settings'];
            
            // 1. Check URL Hash (e.g. #charts)
            const hash = (window.location.hash || '').replace('#', '').trim();
            if (validTabs.includes(hash)) {
                return hash;
            }

            // 2. Check Query Params for pagination
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('beta_page')) return 'beta';
            if (urlParams.has('pending_page') || urlParams.has('approved_page')) return 'reviews';
            if (urlParams.has('users_page')) return 'team';
            if (urlParams.has('visits_page')) return 'traffic';

            // 3. Check localStorage (persists across page reloads and sessions)
            try {
                const stored = localStorage.getItem('airsen_admin_active_tab');
                if (stored && validTabs.includes(stored)) {
                    return stored;
                }
            } catch(e) {}

            return 'overview';
        };

        window.switchDashboardTab = function(tabKey) {
            try {
                window.closeMobileSidebar();
                const validTabs = ['overview', 'charts', 'reviews', 'traffic', 'beta', 'team', 'settings'];
                if (!validTabs.includes(tabKey)) tabKey = 'overview';

                try {
                    localStorage.setItem('airsen_admin_active_tab', tabKey);
                    sessionStorage.setItem('airsen_admin_tab', tabKey);
                } catch(e) {}

                // 1. Update Sidebar Active Button
                document.querySelectorAll('.sidebar-nav .nav-item').forEach(function(btn) {
                    if (btn.getAttribute('data-tab') === tabKey) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });

                // 2. Update Content Panes
                document.querySelectorAll('.dash-tab-pane').forEach(function(pane) {
                    pane.classList.remove('active');
                    pane.style.display = 'none';
                });
                const targetPane = document.getElementById('tab-' + tabKey);
                if (targetPane) {
                    targetPane.classList.add('active');
                    targetPane.style.display = 'block';
                }

                // 3. Update Topbar Header Text
                const titleEl = document.getElementById('topbarTitle');
                if (titleEl && window.tabTitles && window.tabTitles[tabKey]) {
                    titleEl.textContent = window.tabTitles[tabKey];
                }

                // 4. Update URL Hash seamlessly without jump
                try {
                    if (window.history && window.history.replaceState) {
                        window.history.replaceState(null, '', '#' + tabKey);
                    } else {
                        window.location.hash = tabKey;
                    }
                } catch(e) {}

                // 5. Scroll to top of content
                try { window.scrollTo({ top: 0, behavior: 'smooth' }); } catch(e) {}

                // 6. Initialize / Resize Chart.js
                if (tabKey === 'charts' || tabKey === 'overview') {
                    setTimeout(function() {
                        if (typeof window.initOrUpdateTrafficChart === 'function') {
                            window.initOrUpdateTrafficChart();
                        }
                        if (typeof window.initOrUpdateDeviceChart === 'function') {
                            window.initOrUpdateDeviceChart();
                        }
                    }, 40);
                    setTimeout(function() {
                        if (typeof window.initOrUpdateTrafficChart === 'function') {
                            window.initOrUpdateTrafficChart();
                        }
                        if (typeof window.initOrUpdateDeviceChart === 'function') {
                            window.initOrUpdateDeviceChart();
                        }
                    }, 150);
                }

                // 7. Counter animation
                try {
                    if (tabKey === 'overview' && typeof window.animateCounters === 'function') {
                        window.animateCounters();
                    }
                } catch(e) {}

                // 8. Beta notifications
                try {
                    if (tabKey === 'beta') {
                        localStorage.setItem('airsen_seen_beta_count', '{{ $betaSubscribersCount ?? 0 }}');
                        if (typeof window.updateNotificationBadge === 'function') {
                            window.updateNotificationBadge();
                        }
                    }
                } catch(e) {}
            } catch(err) {
                console.error('switchDashboardTab error:', err);
            }
        };

        window.handleLogoutConfirm = function(e, form) {
            if (e && typeof e.preventDefault === 'function') {
                e.preventDefault();
            }

            function doSubmit() {
                if (form && typeof form.submit === 'function') {
                    form.submit();
                } else {
                    const f = document.querySelector('form[action*="logout"]');
                    if (f) f.submit();
                }
            }

            if (window.Swal && typeof window.Swal.fire === 'function') {
                window.Swal.fire({
                    title: 'Çıxış etmək istəyirsiniz?',
                    text: 'Admin panel sessiyanız sonlandırılacaq.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#24242F',
                    confirmButtonText: 'Bəli, Çıxış et',
                    cancelButtonText: 'Qal',
                    background: '#16161C',
                    color: '#FFFFFF',
                    borderRadius: '18px'
                }).then(function(result) {
                    if (result && (result.isConfirmed || result.value)) {
                        doSubmit();
                    }
                }).catch(function() {
                    if (window.confirm('Admin paneldən çıxış etmək istəyirsiniz?')) {
                        doSubmit();
                    }
                });
                return false;
            }

            if (window.confirm('Admin paneldən çıxış etmək istəyirsiniz?')) {
                doSubmit();
            }
            return false;
        };
    </script>
</head>
<body class="dashboard-body">
    <div class="dashboard-layout">
        
        <!-- Mobile Sidebar Backdrop Overlay -->
        <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeMobileSidebar()"></div>

        <!-- ============================================================
             LEFT SIDEBAR (Clean, Modern & Elegant Navigation)
             ============================================================ -->
        <aside class="dashboard-sidebar" id="dashboardSidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.index') }}" class="sidebar-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="AirSen" class="sidebar-logo-img">
                    <div class="sidebar-brand-text">
                        <span class="brand-name">AirSen</span>
                        <span class="brand-subtitle">İdarəetmə Paneli</span>
                    </div>
                </a>
                <button type="button" class="sidebar-close-btn" onclick="closeMobileSidebar()" aria-label="Menyunu Bağla">✕</button>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-title">ƏSAS MENYU</div>
                <button type="button" class="nav-item active" data-tab="overview" onclick="switchDashboardTab('overview')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    <span>Əsas Səhifə</span>
                </button>

                <button type="button" class="nav-item" data-tab="charts" onclick="switchDashboardTab('charts')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                    <span>Ziyarət Qrafikləri</span>
                </button>

                <button type="button" class="nav-item" data-tab="reviews" onclick="switchDashboardTab('reviews')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <span>Rəylərin İdarə Edilməsi</span>
                    @if($pendingReviewsCount > 0)
                        <span class="nav-badge-pill badge-warn">{{ $pendingReviewsCount }}</span>
                    @endif
                </button>

                <button type="button" class="nav-item" data-tab="traffic" onclick="switchDashboardTab('traffic')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                    <span>Canlı Ziyarətçi Logları</span>
                </button>

                <button type="button" class="nav-item" data-tab="beta" onclick="switchDashboardTab('beta')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <span>Beta Abunəçiləri</span>
                </button>

                <div class="nav-section-title" style="margin-top: 24px;">İDARƏETMƏ & TƏHLÜKƏSİZLİK</div>
                <button type="button" class="nav-item" data-tab="team" onclick="switchDashboardTab('team')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span>Admin & İcazələr</span>
                </button>

                <button type="button" class="nav-item" data-tab="settings" onclick="switchDashboardTab('settings')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                    <span>Google Play & Ayarlar</span>
                </button>
            </nav>

            <!-- User Profile Widget at Sidebar Bottom -->
            <div class="sidebar-user-widget">
                <div class="user-avatar-box">
                    <span>{{ Auth::user()->avatar_initial }}</span>
                </div>
                <div class="user-info-box">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <span class="user-role-badge {{ Auth::user()->role_badge_class }}">
                        {{ Auth::user()->role_title }}
                    </span>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST" onsubmit="return handleLogoutConfirm(event, this);">
                    @csrf
                    <button type="submit" class="sidebar-logout-btn" title="Çıxış et">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ============================================================
             MAIN CONTENT AREA
             ============================================================ -->
        <main class="dashboard-main">
            
            <!-- Topbar Header -->
            <header class="dashboard-topbar">
                <div class="topbar-left">
                    <button type="button" class="btn-sidebar-toggle" id="sidebarToggleBtn" onclick="toggleMobileSidebar()" aria-label="Menyunu Aç">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    </button>
                    <div class="topbar-heading">
                        <h1 class="topbar-page-title" id="topbarTitle">Əsas Səhifə</h1>
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="live-status-indicator" title="Sistem real vaxt rejimində aktivdir">
                        <span class="status-pulse-dot"></span>
                        <span class="status-label">Sistem Aktivdir</span>
                    </div>

                    @php
                        $notifReviewCount = $pendingReviewsCount ?? 0;
                        $notifBetaCount = $betaSubscribersCount ?? 0;
                        $totalNotifications = $notifReviewCount + $notifBetaCount;
                    @endphp

                    <!-- Notification Bell & Dropdown -->
                    <div class="topbar-notification-wrapper" id="topbarNotifWrapper">
                        <button type="button" class="btn-topbar-notification" id="topbarNotifBtn" onclick="toggleNotificationDropdown(event)" title="Bildirişlər">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                            <span class="notif-badge-count" style="display: none;">0</span>
                            <span class="notif-ping-dot" style="display: none;"></span>
                        </button>

                        <div class="topbar-notif-dropdown" id="topbarNotifDropdown" style="display: none;">
                            <div class="notif-dropdown-header">
                                <div class="notif-header-title">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                                    <span>Bildirişlər</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span class="notif-header-badge notif-all-clear" id="notifHeaderBadge">Aktiv</span>
                                    <button type="button" onclick="markAllNotificationsRead(event)" class="notif-clear-all-btn" title="Oxunmuş qeyd et" style="background: none; border: none; color: var(--text-muted, #9CA3AF); font-size: 11px; cursor: pointer; text-decoration: underline; padding: 0;">Təmizlə</button>
                                </div>
                            </div>

                            <div class="notif-dropdown-body" id="notifDropdownBody">
                                <a href="#reviews" class="notif-item" id="notifItemReviews" onclick="switchDashboardTab('reviews'); closeNotificationDropdown();" style="display: none;">
                                    <div class="notif-item-icon icon-yellow">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    </div>
                                    <div class="notif-item-content">
                                        <div class="notif-item-title" id="notifReviewTitle">0 yeni rəy təsdiq gözləyir</div>
                                        <div class="notif-item-desc">Premoderasiya üçün rəylər bölməsinə keçin</div>
                                    </div>
                                </a>

                                <a href="#beta" class="notif-item" id="notifItemBeta" onclick="markBetaNotifRead(); switchDashboardTab('beta'); closeNotificationDropdown();" style="display: none;">
                                    <div class="notif-item-icon icon-green">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                    </div>
                                    <div class="notif-item-content">
                                        <div class="notif-item-title" id="notifBetaTitle">0 yeni Beta abunəçisi qoşuldu</div>
                                        <div class="notif-item-desc">Gözləmə siyahısını nəzərdən keçirin</div>
                                    </div>
                                </a>

                                <div class="notif-empty-state" id="notifEmptyState" style="display: none;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--green-signal)" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    <span>Yeni bildiriş yoxdur</span>
                                    <small>Bütün rəylər və gözləmə siyahısı oxunub</small>
                                </div>
                            </div>

                            <div class="notif-dropdown-footer">
                                <span>AirSen Real-Time Monitor</span>
                                <span class="notif-system-live-dot"></span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('landing.index') }}" target="_blank" class="btn-topbar-link" title="Saytı Aç">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                        <span class="topbar-btn-text">Saytı Aç</span>
                    </a>

                    <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;" onsubmit="return handleLogoutConfirm(event, this);">
                        @csrf
                        <button type="submit" class="btn-topbar-logout" title="Sistemdən çıxış et">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                            <span class="topbar-btn-text">Çıxış</span>
                        </button>
                    </form>
                </div>
            </header>

            <div class="dashboard-content-scroll">
                
                <!-- ========================================================
                     TAB 1: Overview & 6 Key Metric Cards
                     ======================================================== -->
                <div id="tab-overview" class="dash-tab-pane active">
                    <div class="metrics-grid">
                        <!-- Card 1: Total Visits (Clickable -> Traffic Logs) -->
                        <div class="metric-card card-purple-glow clickable-metric" onclick="switchDashboardTab('traffic')" title="Canlı loglara baxmaq üçün klikləyin">
                            <div class="metric-icon-box icon-purple">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </div>
                            <div class="metric-content">
                                <span class="metric-label">ÜMUMİ BAXIŞ SAYI</span>
                                <span class="metric-number counter-animate" data-target="{{ $totalVisits }}">{{ number_format($totalVisits) }}</span>
                                <span class="metric-footer-text">Bütün səhifə baxışları</span>
                            </div>
                        </div>

                        <!-- Card 2: Unique Visitors (Clickable -> Charts) -->
                        <div class="metric-card card-cyan-glow clickable-metric" onclick="switchDashboardTab('charts')" title="Ziyarət qrafiklərinə baxmaq üçün klikləyin">
                            <div class="metric-icon-box icon-cyan">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                            </div>
                            <div class="metric-content">
                                <span class="metric-label">UNİKAL ZİYARƏTÇİLƏR</span>
                                <span class="metric-number counter-animate" data-target="{{ $uniqueVisitors }}">{{ number_format($uniqueVisitors) }}</span>
                                <span class="metric-footer-text">Fərqli IP ünvanları</span>
                            </div>
                        </div>

                        <!-- Card 3: Google Play CTA Clicks (Clickable -> Settings) -->
                        <div class="metric-card card-green-glow clickable-metric" onclick="switchDashboardTab('settings')" title="Google Play tənzimləmələrinə keçid">
                            <div class="metric-icon-box icon-green">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                            </div>
                            <div class="metric-content">
                                <span class="metric-label">GOOGLE PLAY KLİKLƏRİ</span>
                                <span class="metric-number counter-animate" data-target="{{ $playStoreClicks }}">{{ number_format($playStoreClicks) }}</span>
                                <span class="metric-footer-text">Tətbiq marağı və CTA</span>
                            </div>
                        </div>

                        <!-- Card 4: CO Gas Simulations Run (Static - No module) -->
                        <div class="metric-card card-red-glow static-metric" title="İnteraktiv test sayı göstəricisi">
                            <div class="metric-icon-box icon-red">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            </div>
                            <div class="metric-content">
                                <span class="metric-label">QAZ SİMULYASİYASI</span>
                                <span class="metric-number counter-animate" data-target="{{ $simulatorRuns }}">{{ number_format($simulatorRuns) }}</span>
                                <span class="metric-footer-text">İnteraktiv testlər</span>
                            </div>
                        </div>

                        <!-- Card 5: Approved Reviews (Clickable -> Reviews) -->
                        <div class="metric-card card-yellow-glow clickable-metric" onclick="switchDashboardTab('reviews')" title="Dərc olunmuş rəylərə keçid">
                            <div class="metric-icon-box icon-yellow">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            </div>
                            <div class="metric-content">
                                <span class="metric-label">DƏRC OLUNMUŞ RƏYLƏR</span>
                                <span class="metric-number counter-animate" data-target="{{ $approvedReviewsCount }}">{{ number_format($approvedReviewsCount) }}</span>
                                <span class="metric-footer-text">Saytda görünən rəylər</span>
                            </div>
                        </div>

                        <!-- Card 6: Pending Premoderation Reviews (Clickable -> Reviews) -->
                        <div class="metric-card card-purple-glow clickable-metric" onclick="switchDashboardTab('reviews')" title="Təsdiq gözləyən rəylərə keçid">
                            <div class="metric-icon-box icon-purple">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            <div class="metric-content">
                                <span class="metric-label">GÖZLƏYƏN RƏYLƏR</span>
                                <span class="metric-number counter-animate" data-target="{{ $pendingReviewsCount }}">{{ number_format($pendingReviewsCount) }}</span>
                                <span class="metric-footer-text">Təsdiq gözləyən rəylər</span>
                            </div>
                        </div>

                        <!-- Card 7: Beta Waitlist Subscribers (Clickable -> Beta) -->
                        <div class="metric-card card-green-glow clickable-metric" onclick="switchDashboardTab('beta')" title="Beta abunəçilərini idarə etmək üçün keçid">
                            <div class="metric-icon-box icon-green">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </div>
                            <div class="metric-content">
                                <span class="metric-label">BETA ABUNƏÇİLƏRİ</span>
                                <span class="metric-number counter-animate" data-target="{{ $betaSubscribersCount ?? 0 }}">{{ number_format($betaSubscribersCount ?? 0) }}</span>
                                <span class="metric-footer-text">3 ay pulsuz qeydiyyatlar</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Overview Summary Box -->
                    <div class="dash-card" style="margin-top: 24px;">
                        <div class="dash-card-header">
                            <div>
                                <h3 class="dash-card-title">⚡ Sürətli Keçidlər & Sistem Vəziyyəti</h3>
                                <p class="dash-card-subtitle">Menyudan istənilən bölməyə keçid edərək ətraflı idarəetmə apara bilərsiniz.</p>
                            </div>
                        </div>
                        <div class="quick-nav-cards-grid">
                            <button type="button" class="quick-nav-card" onclick="switchDashboardTab('charts')">
                                <span class="quick-nav-icon">📈</span>
                                <div>
                                    <strong class="quick-nav-title">Ziyarət Qrafikləri</strong>
                                    <p class="quick-nav-desc">7 günlük dinamika və cihaz analitikası</p>
                                </div>
                            </button>

                            <button type="button" class="quick-nav-card" onclick="switchDashboardTab('reviews')">
                                <span class="quick-nav-icon">📝</span>
                                <div>
                                    <strong class="quick-nav-title">Rəylərin İdarə Edilməsi</strong>
                                    <p class="quick-nav-desc">{{ $pendingReviewsCount }} yeni rəy təsdiq gözləyir</p>
                                </div>
                            </button>

                            <button type="button" class="quick-nav-card" onclick="switchDashboardTab('traffic')">
                                <span class="quick-nav-icon">🌐</span>
                                <div>
                                    <strong class="quick-nav-title">Canlı Loglar</strong>
                                    <p class="quick-nav-desc">Real-time daxilolma və hadisə axını</p>
                                </div>
                            </button>

                            <button type="button" class="quick-nav-card" onclick="switchDashboardTab('team')">
                                <span class="quick-nav-icon">👥</span>
                                <div>
                                    <strong class="quick-nav-title">Admin Heyəti & İcazələr</strong>
                                    <p class="quick-nav-desc">{{ $adminUsers->total() }} aktiv idarəçi hesabı</p>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ========================================================
                     TAB 2: Interactive Charts & Analytics
                     ======================================================== -->
                <div id="tab-charts" class="dash-tab-pane">
                    <div class="charts-row">
                        <!-- Traffic Dynamics Chart -->
                        <div class="dash-card chart-large-card">
                            <div class="dash-card-header">
                                <div>
                                    <h3 class="dash-card-title">📈 Son 7 Günün Ziyarətçi Dinamikası</h3>
                                    <p class="dash-card-subtitle">Gündəlik ümumi baxış və unikal ziyarətçilərin qrafiki</p>
                                </div>
                                <div class="chart-legend-custom">
                                    <span class="legend-item"><span class="legend-dot dot-purple"></span> Ümumi Baxış</span>
                                    <span class="legend-item"><span class="legend-dot dot-cyan"></span> Unikal Ziyarətçi</span>
                                </div>
                            </div>
                            <div class="chart-canvas-container">
                                <canvas id="trafficDynamicsChart"></canvas>
                            </div>
                        </div>

                        <!-- Device & Language Distribution Doughnut -->
                        <div class="dash-card chart-small-card">
                            <div class="dash-card-header">
                                <div>
                                    <h3 class="dash-card-title">📱 Cihazlar & Dillər</h3>
                                    <p class="dash-card-subtitle">İstifadəçi avadanlıqları bölgüsü</p>
                                </div>
                            </div>
                            <div class="donut-chart-container">
                                <canvas id="deviceDistributionChart"></canvas>
                            </div>
                            <div class="device-stats-list">
                                @forelse($deviceStats as $dev)
                                    @php
                                        $dIcon = str_contains($dev->device_name, 'Smartfon') ? '📱' : (str_contains($dev->device_name, 'Planşet') ? '📟' : '💻');
                                    @endphp
                                    <div class="device-stat-row">
                                        <span class="device-name">{{ $dIcon }} {{ $dev->device_name }}</span>
                                        <span class="device-count">{{ number_format($dev->total) }} baxış</span>
                                    </div>
                                @empty
                                    <p style="color: var(--text-dim); font-size: 13px; text-align: center;">Məlumat toplanır...</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================================
                     TAB 3: Review Premoderation Queue
                     ======================================================== -->
                <div id="tab-reviews" class="dash-tab-pane">
                    <div class="dash-card">
                        <div class="dash-card-header">
                            <div>
                                <h3 class="dash-card-title">📝 Təsdiq Gözləyən Rəylər</h3>
                                <p class="dash-card-subtitle">Saytda dərc edilməzdən əvvəl yoxlanılmalı olan yeni rəylər.</p>
                            </div>
                            <span class="status-pill {{ $pendingReviews->total() > 0 ? 'pill-pending' : 'pill-approved' }}">
                                {{ $pendingReviews->total() }} Gözləyən
                            </span>
                        </div>

                        @if($pendingReviews->isEmpty())
                            <div class="empty-state-box">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--green-signal)" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <p>Bütün rəylər nəzərdən keçirilib! Hazırda təsdiq gözləyən yeni rəy yoxdur.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="dash-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Müəllif</th>
                                            <th>Reytinq</th>
                                            <th>Rəy Mətni</th>
                                            <th>Tarix</th>
                                            <th>Əməliyyatlar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingReviews as $rev)
                                            <tr>
                                                 <td><span class="id-tag">#{{ $rev->id }}</span></td>
                                                <td><strong>{{ $rev->name }}</strong></td>
                                                <td>
                                                    <span class="rating-badge-inline">
                                                        {{ number_format($rev->rating, 1) }} ★
                                                    </span>
                                                </td>
                                                <td class="comment-cell">{{ $rev->comment }}</td>
                                                <td class="date-cell">{{ $rev->created_at->format('d.m.Y H:i') }}</td>
                                                <td>
                                                    <div class="action-buttons-group">
                                                        <form action="{{ route('admin.reviews.approve', $rev->id) }}" method="POST" onsubmit="return handleReviewApprove(event, this, '{{ addslashes($rev->name) }}');">
                                                             @csrf
                                                            <button type="submit" class="btn-action-approve" title="Təsdiqlə və saytda dərc et">
                                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('admin.reviews.delete', $rev->id) }}" method="POST" onsubmit="return handleReviewDelete(event, this, '{{ addslashes($rev->name) }}');">
                                                            @csrf
                                                            <button type="submit" class="btn-action-delete" title="Rəyi sil">
                                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="pagination-wrapper">
                                {{ $pendingReviews->links('admin.pagination') }}
                            </div>
                        @endif
                    </div>

                    <!-- Approved Reviews List -->
                    <div class="dash-card" style="margin-top: 24px;">
                        <div class="dash-card-header">
                            <div>
                                <h3 class="dash-card-title">⭐ Saytda Dərc Olunmuş Rəylər</h3>
                                <p class="dash-card-subtitle">Hazırda ana səhifədə aktiv görünən təsdiqlənmiş rəylər.</p>
                            </div>
                            <span class="status-pill pill-approved">{{ $approvedReviews->total() }} Dərc edilmiş</span>
                        </div>

                        @if($approvedReviews->isEmpty())
                            <div class="empty-state-box">
                                <p>Hələlik heç bir rəy dərc edilməyib.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="dash-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Müəllif</th>
                                            <th>Reytinq</th>
                                            <th>Rəy Mətni</th>
                                            <th>Tarix</th>
                                            <th>Əməliyyat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($approvedReviews as $rev)
                                            <tr>
                                                <td><span class="id-tag">#{{ $rev->id }}</span></td>
                                                <td><strong>{{ $rev->name }}</strong></td>
                                                <td>
                                                    <span class="rating-badge-inline">
                                                        {{ number_format($rev->rating, 1) }} ★
                                                    </span>
                                                </td>
                                                <td class="comment-cell">{{ $rev->comment }}</td>
                                                <td class="date-cell">{{ $rev->created_at->format('d.m.Y H:i') }}</td>
                                                <td>
                                                    <form action="{{ route('admin.reviews.delete', $rev->id) }}" method="POST" onsubmit="return handleReviewDelete(event, this, '{{ addslashes($rev->name) }}');">
                                                        @csrf
                                                        <button type="submit" class="btn-action-delete" title="Rəyi sil">
                                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="pagination-wrapper">
                                {{ $approvedReviews->links('admin.pagination') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- ========================================================
                     TAB 4: Real-Time Traffic Stream Logs
                     ======================================================== -->
                <div id="tab-traffic" class="dash-tab-pane">
                    <div class="dash-card">
                        <div class="dash-card-header">
                            <div>
                                <h3 class="dash-card-title">🌐 Son Hadisələr Axını</h3>
                                <p class="dash-card-subtitle">Real vaxt daxilolmalar və hadisə qeydləri.</p>
                            </div>
                            <span class="status-pill pill-cyan">Canlı Axın</span>
                        </div>

                        <div class="table-responsive">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Hadisə Növü</th>
                                        <th>Cihaz</th>
                                        <th>Brauzer & ƏS</th>
                                        <th>Dil</th>
                                        <th>Səhifə / Keçid</th>
                                        <th>Tarix</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentVisits as $visit)
                                        <tr>
                                            <td>
                                                <span class="event-pill {{ $visit->event_type === 'google_play_click' ? 'event-green' : ($visit->event_type === 'review_submitted' ? 'event-purple' : 'event-dim') }}">
                                                    {{ $visit->event_type }}
                                                </span>
                                            </td>
                                            <td>{{ $visit->device ?? 'Desktop' }}</td>
                                            <td>{{ $visit->browser }} ({{ $visit->os }})</td>
                                            <td><strong class="lang-tag">{{ strtoupper($visit->language ?? 'AZ') }}</strong></td>
                                            <td class="url-cell">{{ $visit->page_url }}</td>
                                            <td class="date-cell">{{ $visit->created_at->format('d.m.Y H:i:s') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" style="text-align: center; padding: 24px; color: var(--text-dim);">Hələlik qeydə alınmış hadisə yoxdur.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="pagination-wrapper">
                            {{ $recentVisits->links('admin.pagination') }}
                        </div>
                    </div>
                </div>

                <!-- ========================================================
                     TAB: Beta Test Early Access Subscribers
                     ======================================================== -->
                <div id="tab-beta" class="dash-tab-pane">
                    <div class="dash-card">
                        <div class="dash-card-header">
                            <div>
                                <h3 class="dash-card-title">🎁 Qapalı Beta Abunəçiləri & Erkən Giriş</h3>
                                <p class="dash-card-subtitle">AirSen Premium 3 ay pulsuz istifadə hüququ qazanan istifadəçilərin rəsmi qeydiyyat bazası.</p>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                <span class="status-pill pill-green">{{ $betaWaitlist->total() }} Abunəçi</span>
                                @if($betaWaitlist->total() > 0)
                                    <button type="button" class="btn-topbar-link" onclick="copyAllBetaEmails()" title="Bütün e-poçtları kopyala" style="font-size: 11.5px; padding: 6px 12px; cursor: pointer;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                        <span>E-poçtları Kopyala</span>
                                    </button>
                                    <button type="button" class="btn-topbar-link" onclick="exportBetaCSV()" title="Excel faylı kimi yüklə" style="font-size: 11.5px; padding: 6px 12px; cursor: pointer;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        <span>Excel İxrac Et</span>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="dash-table" id="betaSubscribersTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>E-poçt Ünvanı</th>
                                        <th>Dil</th>
                                        <th>Məxfilik</th>
                                        <th>Qeydiyyat Tarixi</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($betaWaitlist as $index => $item)
                                        <tr>
                                            <td><strong>#{{ $betaSubscribersCount - ($betaWaitlist->firstItem() - 1 + $index) }}</strong></td>
                                            <td>
                                                <div style="display: inline-flex; align-items: center; gap: 8px;">
                                                    <strong class="beta-email-text" style="color: var(--blue-neon); font-size: 14px;">{{ $item['email'] ?? '-' }}</strong>
                                                    <button type="button" class="btn-action-edit" onclick="navigator.clipboard.writeText('{{ $item['email'] ?? '' }}'); Swal.fire({title: 'Kopyalandı!', text: '{{ $item['email'] ?? '' }} panoya kopyalandı.', icon: 'success', timer: 1500, showConfirmButton: false, background: '#16161C', color: '#FFF'});" title="E-poçtu kopyala" style="padding: 3px 6px; font-size: 11px; cursor: pointer;">
                                                        📋
                                                    </button>
                                                </div>
                                            </td>
                                            <td><span class="lang-tag">{{ strtoupper($item['locale'] ?? 'AZ') }}</span></td>
                                            <td style="font-size: 12px; color: var(--text-dim);">IP saxlanılmır</td>
                                            <td class="date-cell">{{ $item['created_at'] ?? now()->format('d.m.Y H:i') }}</td>
                                            <td><span class="status-pill pill-green">🎁 3 Ay Pulsuz Aktiv</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" style="text-align: center; padding: 32px; color: var(--text-dim);">
                                                Hələlik heç bir beta abunəçisi qeydiyyatdan keçməyib.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="pagination-wrapper">
                            {{ $betaWaitlist->links('admin.pagination') }}
                        </div>
                    </div>
                </div>

                <!-- ========================================================
                     TAB 5: Team & Admin User Management
                     ======================================================== -->
                <div id="tab-team" class="dash-tab-pane">
                    <div class="{{ Auth::user()->isSuperAdmin() ? 'team-grid-layout' : 'team-single-layout' }}">
                        <!-- Add New User Form -->
                        @if(Auth::user()->isSuperAdmin())
                            <div class="dash-card">
                                <div class="dash-card-header">
                                    <div>
                                        <h3 class="dash-card-title">➕ Yeni Admin / Moderator Əlavə Et</h3>
                                        <p class="dash-card-subtitle">Komanda üzvünün qeydiyyatı və hüquq təyini.</p>
                                    </div>
                                </div>
                                <form action="{{ route('admin.users.store') }}" method="POST" class="admin-create-form">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">Ad və Soyad</label>
                                        <input type="text" name="name" class="form-input" placeholder="Məs: Əli Məmmədov" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">E-poçt ünvanı</label>
                                        <input type="email" name="email" class="form-input" placeholder="ali@airsen.com" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Şifrə</label>
                                        <div style="position: relative; display: flex; align-items: center;">
                                            <input type="password" name="password" id="createUserPassword" class="form-input" placeholder="Minimum 12 simvol" required minlength="12" style="width: 100%; padding-right: 42px;">
                                            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('createUserPassword', this)" aria-label="Şifrəni göstər/gizlət">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">İcazə Rolu</label>
                                        <select name="role" class="form-input">
                                            <option value="moderator">🛡️ Moderator (Yalnız Rəy İdarəetməsi)</option>
                                            <option value="analyst">📊 Analitik (Yalnız Statistika & Baxış)</option>
                                            <option value="super_admin">👑 Super Admin (Tam Hüquqlu)</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn-primary-glow" style="width: 100%;">
                                        İstifadəçini Yarat
                                    </button>
                                </form>
                            </div>
                        @endif

                        <!-- Existing Users List -->
                        <div class="dash-card" style="flex: 1; width: 100%;">
                            <div class="dash-card-header">
                                <div>
                                    <h3 class="dash-card-title">👥 Mövcud Admin Heyəti</h3>
                                    <p class="dash-card-subtitle">Qeydiyyatdan keçmiş idarəçi və moderatorlar.</p>
                                </div>
                                <span class="status-pill pill-purple">{{ $adminUsers->total() }} İstifadəçi</span>
                            </div>

                            <div class="table-responsive">
                                <table class="dash-table">
                                    <thead>
                                        <tr>
                                            <th>İstifadəçi</th>
                                            <th>Email</th>
                                            <th>Rol</th>
                                            <th>Qeydiyyat Tarixi</th>
                                            @if(Auth::user()->isSuperAdmin())
                                                <th>Əməliyyat</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($adminUsers as $usr)
                                            <tr>
                                                <td>
                                                    <div class="user-cell">
                                                        <div class="user-mini-avatar">{{ $usr->avatar_initial }}</div>
                                                        <strong>{{ $usr->name }}</strong>
                                                    </div>
                                                </td>
                                                <td>{{ $usr->email }}</td>
                                                <td>
                                                    <span class="user-role-badge {{ $usr->role_badge_class }}">
                                                        {{ $usr->role_title }}
                                                    </span>
                                                </td>
                                                <td class="date-cell">{{ $usr->created_at->format('d.m.Y') }}</td>
                                                @if(Auth::user()->isSuperAdmin())
                                                    <td>
                                                        <div class="action-buttons-group">
                                                            <button type="button" class="btn-action-edit" title="İstifadəçini redaktə et" 
                                                                data-id="{{ $usr->id }}" 
                                                                data-name="{{ $usr->name }}" 
                                                                data-email="{{ $usr->email }}" 
                                                                data-role="{{ $usr->role }}" 
                                                                onclick="openEditUserModal(this, event)">
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                            </button>
                                                            @if($usr->id !== Auth::id())
                                                                <form action="{{ route('admin.users.delete', $usr->id) }}" method="POST" onsubmit="return handleUserDelete(event, this, '{{ addslashes($usr->name) }}');">
                                                                    @csrf
                                                                    <button type="submit" class="btn-action-delete" title="İstifadəçini sil">
                                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <span style="font-size: 11px; color: var(--text-dim); margin-left: 4px;">(Siz)</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="pagination-wrapper">
                                {{ $adminUsers->links('admin.pagination') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================================
                     TAB 6: Google Play & App Settings
                     ======================================================== -->
                <div id="tab-settings" class="dash-tab-pane">
                    @if(Auth::user()->isSuperAdmin())
                        <div class="dash-card">
                            <div class="dash-card-header">
                                <div>
                                    <h3 class="dash-card-title">🚀 Google Play Düyməsi İdarəetməsi</h3>
                                    <p class="dash-card-subtitle">Tətbiq yoxlanışda olduqda "Moderasiya Rejimi", çıxdıqda "Aktiv Rejim" seçin.</p>
                                </div>
                                <span class="status-pill {{ $googlePlayStatus === 'active' ? 'pill-approved' : 'pill-pending' }}">
                                    Status: {{ strtoupper($googlePlayStatus) }}
                                </span>
                            </div>

                            <form action="{{ route('admin.settings.google-play') }}" method="POST" class="settings-form">
                                @csrf
                                <div class="form-grid-2">
                                    <div class="form-group">
                                        <label class="form-label">Düymə Rejimi</label>
                                        <select name="status" class="form-input">
                                            <option value="moderation" {{ $googlePlayStatus === 'moderation' ? 'selected' : '' }}>🟡 Moderasiya Rejimi (Məlumatlandırıcı Modal Açılır)</option>
                                            <option value="active" {{ $googlePlayStatus === 'active' ? 'selected' : '' }}>🟢 Aktiv Rejim (Birbaşa Google Play Linki Açılır)</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Google Play Linki (URL)</label>
                                        <input type="url" name="url" value="{{ $googlePlayUrl }}" class="form-input" placeholder="https://play.google.com/store/apps/details?id=com.airsen.app" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn-primary-glow" style="margin-top: 10px;">
                                    Tənzimləmələri Yadda Saxla
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- User Profile & Password Update -->
                    <div class="dash-card" style="{{ Auth::user()->isSuperAdmin() ? 'margin-top: 24px;' : '' }}">
                        <div class="dash-card-header">
                            <div>
                                <h3 class="dash-card-title">🔐 Şəxsi Profil & Şifrə Dəyişdirilməsi</h3>
                                <p class="dash-card-subtitle">Cari hesabınızın adını, emailini və ya giriş şifrəsini yeniləyin.</p>
                            </div>
                        </div>

                        <form action="{{ route('admin.profile.update') }}" method="POST" class="settings-form">
                            @csrf
                            <div class="form-grid-3">
                                <div class="form-group">
                                    <label class="form-label">Cari Şifrə</label>
                                    <div style="position: relative; display: flex; align-items: center;">
                                        <input type="password" name="current_password" id="profileCurrentPassword" class="form-input" placeholder="Dəyişikliyi təsdiqləyin" required autocomplete="current-password" style="width: 100%; padding-right: 42px;">
                                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('profileCurrentPassword', this)" aria-label="Şifrəni göstər/gizlət">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Adınız</label>
                                    <input type="text" name="name" value="{{ Auth::user()->name }}" class="form-input" placeholder="Məs: Əli Məmmədov" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" value="{{ Auth::user()->email }}" class="form-input" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Yeni Şifrə (Dəyişmək istəmirsinizsə boş buraxın)</label>
                                    <div style="position: relative; display: flex; align-items: center;">
                                        <input type="password" name="password" id="profileNewPassword" class="form-input" placeholder="Yeni şifrə (min 12 simvol)" minlength="12" style="width: 100%; padding-right: 42px;">
                                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('profileNewPassword', this)" aria-label="Şifrəni göstər/gizlət">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn-primary-glow" style="margin-top: 10px;">
                                Profil Məlumatlarını Yenilə
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- ============================================================
         Chart.js Initialization & Tab Switching Scripts
         ============================================================ -->
    <script>
        // Password Visibility Toggle Helper (Eye icon)
        window.togglePasswordVisibility = function(inputId, btn) {
            const input = typeof inputId === 'string' ? document.getElementById(inputId) : inputId;
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            
            if (btn) {
                if (isPassword) {
                    btn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>`;
                    btn.style.color = '#C042F0';
                } else {
                    btn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>`;
                    btn.style.color = '#9CA3AF';
                }
            }
        };

        // Modal Handlers for Edit User
        window.openEditUserModal = function(target, event) {
            if (event) {
                if (typeof event.preventDefault === 'function') event.preventDefault();
                if (typeof event.stopPropagation === 'function') event.stopPropagation();
            }

            let btn = null;
            if (target && target.nodeType) {
                btn = (typeof target.closest === 'function') ? (target.closest('.btn-action-edit') || target) : target;
            }

            let id = '', name = '', email = '', role = 'moderator';
            if (btn && btn.getAttribute) {
                id = btn.getAttribute('data-id') || '';
                name = btn.getAttribute('data-name') || '';
                email = btn.getAttribute('data-email') || '';
                role = btn.getAttribute('data-role') || 'moderator';
            } else if (arguments.length >= 4) {
                id = arguments[0];
                name = arguments[1];
                email = arguments[2];
                role = arguments[3];
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            function escapeHtml(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            const modalHtml = `
                <form id="swalEditUserForm" action="/admin/users/${id}/update" method="POST" style="text-align: left; margin-top: 14px;">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #9CA3AF; margin-bottom: 6px;">Ad və Soyad</label>
                        <input type="text" name="name" id="swalEditName" class="form-input" style="width: 100%; box-sizing: border-box; background: #0F0F14; border: 1px solid rgba(255,255,255,0.14); border-radius: 12px; color: #FFF; padding: 11px 14px; font-size: 14px;" value="${escapeHtml(name)}" required placeholder="Məs: Əli Məmmədov">
                    </div>
                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #9CA3AF; margin-bottom: 6px;">E-poçt ünvanı</label>
                        <input type="email" name="email" id="swalEditEmail" class="form-input" style="width: 100%; box-sizing: border-box; background: #0F0F14; border: 1px solid rgba(255,255,255,0.14); border-radius: 12px; color: #FFF; padding: 11px 14px; font-size: 14px;" value="${escapeHtml(email)}" required placeholder="ali@airsen.com">
                    </div>
                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #9CA3AF; margin-bottom: 6px;">İcazə Rolu</label>
                        <select name="role" id="swalEditRole" class="form-input" style="width: 100%; box-sizing: border-box; background: #0F0F14; border: 1px solid rgba(255,255,255,0.14); border-radius: 12px; color: #FFF; padding: 11px 14px; font-size: 14px;">
                            <option value="moderator" ${role === 'moderator' ? 'selected' : ''}>🛡️ Moderator (Yalnız Rəy İdarəetməsi)</option>
                            <option value="analyst" ${role === 'analyst' ? 'selected' : ''}>📊 Analitik (Yalnız Statistika & Baxış)</option>
                            <option value="super_admin" ${role === 'super_admin' ? 'selected' : ''}>👑 Super Admin (Tam Hüquqlu)</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 6px;">
                        <label class="form-label" style="display: block; font-size: 12.5px; font-weight: 700; color: #9CA3AF; margin-bottom: 6px;">Yeni Şifrə (Dəyişmək istəmirsinizsə boş saxlayın)</label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <input type="password" name="password" id="swalEditPassword" class="form-input" style="width: 100%; box-sizing: border-box; background: #0F0F14; border: 1px solid rgba(255,255,255,0.14); border-radius: 12px; color: #FFF; padding: 11px 42px 11px 14px; font-size: 14px;" placeholder="Dəyişmək istəmirsinizsə boş saxlayın" minlength="6">
                            <button type="button" onclick="togglePasswordVisibility('swalEditPassword', this)" style="position: absolute; right: 8px; background: transparent; border: none; color: #9CA3AF; cursor: pointer; padding: 6px; display: flex; align-items: center; justify-content: center; transition: color 0.2s; border-radius: 6px;" aria-label="Şifrəni göstər/gizlət">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>
                </form>
            `;

            if (window.Swal && typeof window.Swal.fire === 'function') {
                window.Swal.fire({
                    title: '✏️ İstifadəçi Məlumatlarını Redaktə Et',
                    html: modalHtml,
                    showCancelButton: true,
                    confirmButtonColor: '#C042F0',
                    cancelButtonColor: '#24242F',
                    confirmButtonText: 'Yadda Saxla',
                    cancelButtonText: 'Ləğv et',
                    background: '#16161C',
                    color: '#FFFFFF',
                    borderRadius: '24px',
                    focusConfirm: false,
                    didOpen: () => {
                        const input = document.getElementById('swalEditName');
                        if (input) input.focus();
                    },
                    preConfirm: () => {
                        const nameVal = document.getElementById('swalEditName')?.value?.trim();
                        const emailVal = document.getElementById('swalEditEmail')?.value?.trim();
                        const passwordVal = document.getElementById('swalEditPassword')?.value;
                        if (!nameVal) {
                            if (window.Swal.showValidationMessage) window.Swal.showValidationMessage('Zəhmət olmasa istifadəçinin adını daxil edin.');
                            return false;
                        }
                        if (!emailVal) {
                            if (window.Swal.showValidationMessage) window.Swal.showValidationMessage('Zəhmət olmasa e-poçt ünvanını daxil edin.');
                            return false;
                        }
                        if (passwordVal && passwordVal.length < 6) {
                            if (window.Swal.showValidationMessage) window.Swal.showValidationMessage('Şifrə minimum 6 simvol olmalıdır.');
                            return false;
                        }
                        const form = document.getElementById('swalEditUserForm');
                        if (form) form.submit();
                        return true;
                    }
                });
            }
        };

        // Custom Styled SweetAlert for Review Deletion
        window.handleReviewDelete = function(e, form, authorName) {
            if (e && typeof e.preventDefault === 'function') e.preventDefault();
            if (window.Swal && typeof window.Swal.fire === 'function') {
                window.Swal.fire({
                    title: 'Rəyi silmək istəyirsiniz?',
                    text: `${authorName} tərəfindən yazılmış bu rəy sistemdən həmişəlik silinəcək.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#24242F',
                    confirmButtonText: 'Bəli, Sil!',
                    cancelButtonText: 'Ləğv et',
                    background: '#16161C',
                    color: '#FFFFFF',
                    borderRadius: '18px'
                }).then((result) => {
                    if (result && (result.isConfirmed || result.value)) {
                        form.submit();
                    }
                });
                return false;
            }
            if (window.confirm(`"${authorName}" tərəfindən yazılmış rəyi silmək istəyirsiniz?`)) {
                form.submit();
            }
            return false;
        };

        // Custom Styled SweetAlert for Review Approval
        window.handleReviewApprove = function(e, form, authorName) {
            if (e && typeof e.preventDefault === 'function') e.preventDefault();
            if (window.Swal && typeof window.Swal.fire === 'function') {
                window.Swal.fire({
                    title: 'Rəyi təsdiqləmək istəyirsiniz?',
                    text: `"${authorName}" tərəfindən yazılmış rəy ana səhifədə canlı dərc olunacaq.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#76FF5B',
                    cancelButtonColor: '#24242F',
                    confirmButtonText: 'Bəli, Təsdiqlə!',
                    cancelButtonText: 'Ləğv et',
                    background: '#16161C',
                    color: '#FFFFFF',
                    borderRadius: '18px'
                }).then((result) => {
                    if (result && (result.isConfirmed || result.value)) {
                        form.submit();
                    }
                });
                return false;
            }
            if (window.confirm(`"${authorName}" tərəfindən yazılmış rəyi təsdiqləmək istəyirsiniz?`)) {
                form.submit();
            }
            return false;
        };

        // Custom Styled SweetAlert for User Deletion
        window.handleUserDelete = function(e, form, userName) {
            if (e && typeof e.preventDefault === 'function') e.preventDefault();
            if (window.Swal && typeof window.Swal.fire === 'function') {
                window.Swal.fire({
                    title: 'İstifadəçini silmək istəyirsiniz?',
                    text: `"${userName}" adlı istifadəçi/admin sistemdən həmişəlik silinəcək.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#24242F',
                    confirmButtonText: 'Bəli, Sil!',
                    cancelButtonText: 'Ləğv et',
                    background: '#16161C',
                    color: '#FFFFFF',
                    borderRadius: '18px'
                }).then((result) => {
                    if (result && (result.isConfirmed || result.value)) {
                        form.submit();
                    }
                });
                return false;
            }
            if (window.confirm(`"${userName}" adlı istifadəçini silmək istəyirsiniz?`)) {
                form.submit();
            }
            return false;
        };

        // Server-side Flash Alerts
        @if(session('success'))
            try {
                window.Swal.fire({
                    icon: 'success',
                    title: 'Uğurlu Əməliyyat',
                    text: {!! json_encode(session('success')) !!},
                    background: '#16161C',
                    color: '#FFFFFF',
                    confirmButtonColor: '#C042F0',
                    borderRadius: '18px'
                });
            } catch(e) {}
        @endif

        @if(session('error'))
            try {
                window.Swal.fire({
                    icon: 'error',
                    title: 'Xəta Baş Verdi',
                    text: {!! json_encode(session('error')) !!},
                    background: '#16161C',
                    color: '#FFFFFF',
                    confirmButtonColor: '#dc3545',
                    borderRadius: '18px'
                });
            } catch(e) {}
        @endif

        @if($errors->any())
            try {
                window.Swal.fire({
                    icon: 'error',
                    title: 'Məlumat Doğrulanmadı',
                    html: '<div style="text-align: left; padding: 6px 14px; color: #FFA3A3; font-size: 13.5px; line-height: 1.6;"><ul style="margin: 0; padding-left: 18px;">@foreach($errors->all() as $err)<li>{{ addslashes($err) }}</li>@endforeach</ul></div>',
                    background: '#16161C',
                    color: '#FFFFFF',
                    confirmButtonColor: '#dc3545',
                    borderRadius: '18px'
                });
            } catch(e) {}
        @endif

        let trafficChartInstance = null;
        let deviceChartInstance = null;
        window.trafficChartInstance = trafficChartInstance;
        window.deviceChartInstance = deviceChartInstance;

        // Count-Up Animation Function
        window.animateCounters = function() {
            const counters = document.querySelectorAll('.counter-animate');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target') || '0', 10);
                if (isNaN(target)) return;

                const duration = 1000;
                const startTime = performance.now();

                function updateCounter(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const ease = 1 - Math.pow(1 - progress, 3);
                    const currentVal = Math.floor(ease * target);
                    counter.textContent = currentVal.toLocaleString();

                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target.toLocaleString();
                    }
                }
                requestAnimationFrame(updateCounter);
            });
        };

        // Notification Helpers
        window.markBetaNotifRead = function() {
            const currentBeta = {{ $betaSubscribersCount ?? 0 }};
            localStorage.setItem('airsen_seen_beta_count', currentBeta.toString());
            window.updateNotificationBadge();
        };

        window.markAllNotificationsRead = function(event) {
            if (event) event.stopPropagation();
            const currentBeta = {{ $betaSubscribersCount ?? 0 }};
            localStorage.setItem('airsen_seen_beta_count', currentBeta.toString());
            window.updateNotificationBadge();
        };

        window.updateNotificationBadge = function() {
            const pendingReviews = {{ $pendingReviewsCount ?? 0 }};
            const currentBeta = {{ $betaSubscribersCount ?? 0 }};
            const seenBeta = parseInt(localStorage.getItem('airsen_seen_beta_count') || '0', 10);
            const newBeta = Math.max(0, currentBeta - seenBeta);
            const total = pendingReviews + newBeta;

            const badgeCountEl = document.querySelector('#topbarNotifBtn .notif-badge-count');
            const pingDotEl = document.querySelector('#topbarNotifBtn .notif-ping-dot');
            const notifHeaderBadge = document.getElementById('notifHeaderBadge');
            const notifItemReviews = document.getElementById('notifItemReviews');
            const notifItemBeta = document.getElementById('notifItemBeta');
            const notifEmptyState = document.getElementById('notifEmptyState');
            const notifReviewTitle = document.getElementById('notifReviewTitle');
            const notifBetaTitle = document.getElementById('notifBetaTitle');

            if (total > 0) {
                if (badgeCountEl) {
                    badgeCountEl.textContent = total;
                    badgeCountEl.style.display = 'flex';
                }
                if (pingDotEl) pingDotEl.style.display = 'block';
                if (notifHeaderBadge) {
                    notifHeaderBadge.textContent = `${total} yeni`;
                    notifHeaderBadge.classList.remove('notif-all-clear');
                }
            } else {
                if (badgeCountEl) badgeCountEl.style.display = 'none';
                if (pingDotEl) pingDotEl.style.display = 'none';
                if (notifHeaderBadge) {
                    notifHeaderBadge.textContent = 'Aktiv';
                    notifHeaderBadge.classList.add('notif-all-clear');
                }
            }

            if (notifItemReviews) {
                if (pendingReviews > 0) {
                    notifItemReviews.style.display = 'flex';
                    if (notifReviewTitle) notifReviewTitle.textContent = `${pendingReviews} yeni rəy təsdiq gözləyir`;
                } else {
                    notifItemReviews.style.display = 'none';
                }
            }

            if (notifItemBeta) {
                if (newBeta > 0) {
                    notifItemBeta.style.display = 'flex';
                    if (notifBetaTitle) notifBetaTitle.textContent = `${newBeta} yeni Beta abunəçisi qoşuldu`;
                } else {
                    notifItemBeta.style.display = 'none';
                }
            }

            if (notifEmptyState) {
                if (total === 0) {
                    notifEmptyState.style.display = 'flex';
                } else {
                    notifEmptyState.style.display = 'none';
                }
            }
        };

        window.toggleNotificationDropdown = function(event) {
            if (event) event.stopPropagation();
            const dropdown = document.getElementById('topbarNotifDropdown');
            const btn = document.getElementById('topbarNotifBtn');
            if (!dropdown) return;

            const isVisible = dropdown.style.display === 'block';
            dropdown.style.display = isVisible ? 'none' : 'block';
            if (btn) {
                btn.classList.toggle('active', !isVisible);
            }
        };

        window.closeNotificationDropdown = function() {
            const dropdown = document.getElementById('topbarNotifDropdown');
            const btn = document.getElementById('topbarNotifBtn');
            if (dropdown) dropdown.style.display = 'none';
            if (btn) btn.classList.remove('active');
        };

        window.addEventListener('click', (e) => {
            const wrapper = document.getElementById('topbarNotifWrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                window.closeNotificationDropdown();
            }
        });

        // Beta Waitlist Helpers
        window.copyAllBetaEmails = function() {
            const emailEls = document.querySelectorAll('.beta-email-text');
            const emails = Array.from(emailEls).map(el => el.textContent.trim()).filter(e => e && e !== '-');
            if (emails.length === 0) {
                window.Swal.fire({ title: 'Məlumat yoxdur', text: 'Kopyalanacaq e-poçt ünvanı tapılmadı.', icon: 'info', background: '#16161C', color: '#FFF' });
                return;
            }
            const text = emails.join('\n');
            const showSuccess = () => {
                window.Swal.fire({
                    title: 'Kopyalandı',
                    text: `${emails.length} ədəd e-poçt ünvanı panoya kopyalandı.`,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    background: '#16161C',
                    color: '#FFF'
                });
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(showSuccess).catch(() => {
                    fallbackCopyText(text, showSuccess);
                });
            } else {
                fallbackCopyText(text, showSuccess);
            }
        };

        function fallbackCopyText(text, callback) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            if (callback) callback();
        }

        window.exportBetaCSV = function() {
            const table = document.getElementById('betaSubscribersTable');
            if (!table) return;

            const trs = table.querySelectorAll('tbody tr');
            const dataRows = [];
            trs.forEach((tr) => {
                const tds = tr.querySelectorAll('td');
                if (tds.length >= 6) {
                    const id = tds[0].textContent.replace('#', '').trim();
                    const email = tr.querySelector('.beta-email-text')?.textContent.trim() || tds[1].textContent.trim();
                    const lang = tds[2].textContent.trim();
                    const ip = tds[3].textContent.trim();
                    const date = tds[4].textContent.trim();
                    const status = tds[5].textContent.trim();
                    if (email && email !== '-') {
                        dataRows.push({ id, email, lang, ip, date, status });
                    }
                }
            });

            if (dataRows.length === 0) {
                window.Swal.fire({ title: 'Məlumat yoxdur', text: 'İxrac ediləcək beta abunəçisi tapılmadı.', icon: 'info', background: '#16161C', color: '#FFF' });
                return;
            }

            let excelContent = `
<html>
<head>
<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">
<style>
  th { background-color: #6C2BD9; color: #FFFFFF; font-weight: bold; font-family: Segoe UI, Arial, sans-serif; font-size: 11pt; text-align: left; padding: 8px 14px; border: 1px solid #4C1D95; }
  td { font-family: Segoe UI, Arial, sans-serif; font-size: 10.5pt; color: #111827; padding: 7px 14px; border: 1px solid #E5E7EB; mso-number-format:"\\@"; }
  .col-id { text-align: center; font-weight: bold; mso-number-format:"0"; width: 70px; }
  .col-email { color: #2563EB; font-weight: 600; width: 220px; }
  .col-lang { text-align: center; font-weight: bold; width: 70px; }
  .col-ip { font-family: Consolas, monospace; width: 140px; }
  .col-date { width: 190px; mso-number-format:"\\@"; }
  .col-status { color: #059669; font-weight: bold; width: 170px; }
</style>
<\/head>
<body>
  <table>
    <thead>
      <tr>
        <th class="col-id">ID</th>
        <th class="col-email">E-poçt Ünvanı</th>
        <th class="col-lang">Dil</th>
        <th class="col-ip">IP Ünvanı</th>
        <th class="col-date">Qeydiyyat Tarixi</th>
        <th class="col-status">Status</th>
      </tr>
    </thead>
    <tbody>
`;

            dataRows.forEach(row => {
                excelContent += `
      <tr>
        <td class="col-id">#${row.id}</td>
        <td class="col-email">${row.email}</td>
        <td class="col-lang">${row.lang}</td>
        <td class="col-ip">${row.ip}</td>
        <td class="col-date">${row.date}</td>
        <td class="col-status">${row.status}</td>
      </tr>`;
            });

            excelContent += `
    </tbody>
  </table>
<\/body>
</html>`;

            const blob = new Blob(['\uFEFF' + excelContent], { type: 'application/vnd.ms-excel;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `airsen_beta_subscribers_${new Date().toISOString().slice(0, 10)}.xls`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            setTimeout(() => URL.revokeObjectURL(url), 1000);
        };

        // Attach Click to Sidebar Items
        document.querySelectorAll('.sidebar-nav .nav-item').forEach(btn => {
            btn.addEventListener('click', () => {
                const tabKey = btn.getAttribute('data-tab');
                if (tabKey) window.switchDashboardTab(tabKey);
            });
        });

        // Chart 1: Traffic Dynamics (On-Demand & Safe Initialization)
        window.initOrUpdateTrafficChart = function() {
            try {
                if (typeof Chart === 'undefined') return;
                const trafficCanvas = document.getElementById('trafficDynamicsChart');
                if (!trafficCanvas) return;

                const chartsPane = document.getElementById('tab-charts');
                const isChartsActive = chartsPane && (chartsPane.classList.contains('active') || chartsPane.style.display === 'block');
                
                if (!isChartsActive && trafficCanvas.offsetParent === null) {
                    return;
                }

                if (window.trafficChartInstance) {
                    try {
                        window.trafficChartInstance.destroy();
                    } catch(e) {}
                    window.trafficChartInstance = null;
                }

                const ctx = trafficCanvas.getContext('2d');
                const purpleGrad = ctx.createLinearGradient(0, 0, 0, 260);
                purpleGrad.addColorStop(0, 'rgba(192, 66, 240, 0.35)');
                purpleGrad.addColorStop(1, 'rgba(192, 66, 240, 0.0)');

                const cyanGrad = ctx.createLinearGradient(0, 0, 0, 260);
                cyanGrad.addColorStop(0, 'rgba(105, 229, 255, 0.30)');
                cyanGrad.addColorStop(1, 'rgba(105, 229, 255, 0.0)');

                const rawLabels = window.adminChartLabels || [];
                const rawPageviews = window.adminChartPageviews || [];
                const rawVisitors = window.adminChartVisitors || [];

                const labels = (rawLabels && rawLabels.length > 0) ? rawLabels : ['B.e', 'Ç.a', 'Ç.', 'C.a', 'C.', 'Ş.', 'B.'];
                const pageviews = (rawPageviews && rawPageviews.length > 0) ? rawPageviews : [0, 0, 0, 0, 0, 0, 0];
                const visitors = (rawVisitors && rawVisitors.length > 0) ? rawVisitors : [0, 0, 0, 0, 0, 0, 0];

                const maxVal = Math.max(...pageviews, ...visitors, 0);

                window.trafficChartInstance = new Chart(trafficCanvas, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Ümumi Baxış',
                                data: pageviews,
                                borderColor: '#C042F0',
                                backgroundColor: purpleGrad,
                                borderWidth: 2.8,
                                fill: true,
                                tension: 0.38,
                                pointBackgroundColor: '#C042F0',
                                pointBorderColor: '#16161C',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 7
                            },
                            {
                                label: 'Unikal Ziyarətçi',
                                data: visitors,
                                borderColor: '#69E5FF',
                                backgroundColor: cyanGrad,
                                borderWidth: 2.8,
                                fill: true,
                                tension: 0.38,
                                pointBackgroundColor: '#69E5FF',
                                pointBorderColor: '#16161C',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 7
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 350
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(22, 22, 28, 0.95)',
                                titleColor: '#FFFFFF',
                                bodyColor: '#E5E7EB',
                                borderColor: 'rgba(255, 255, 255, 0.12)',
                                borderWidth: 1,
                                padding: 12,
                                cornerRadius: 12,
                                boxPadding: 6,
                                usePointStyle: true
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: { color: '#9CA3AF', font: { family: 'Plus Jakarta Sans', size: 12 } }
                            },
                            y: {
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: { color: '#9CA3AF', font: { family: 'Plus Jakarta Sans', size: 12 }, precision: 0, stepSize: 1 },
                                beginAtZero: true,
                                suggestedMax: maxVal > 5 ? maxVal + 2 : 5
                            }
                        }
                    }
                });
            } catch(err) {
                console.warn('Traffic chart error:', err);
            }
        };

        // Chart 2: Device Distribution (On-Demand & Safe Initialization)
        window.initOrUpdateDeviceChart = function() {
            try {
                if (typeof Chart === 'undefined') return;
                const deviceCanvas = document.getElementById('deviceDistributionChart');
                if (!deviceCanvas) return;

                const chartsPane = document.getElementById('tab-charts');
                const isChartsActive = chartsPane && (chartsPane.classList.contains('active') || chartsPane.style.display === 'block');
                
                if (!isChartsActive && deviceCanvas.offsetParent === null) {
                    return;
                }

                if (window.deviceChartInstance) {
                    try {
                        window.deviceChartInstance.destroy();
                    } catch(e) {}
                    window.deviceChartInstance = null;
                }

                const rawDevLabels = window.adminDeviceLabels || [];
                const rawDevCounts = window.adminDeviceCounts || [];

                const devLabels = (rawDevLabels && rawDevLabels.length > 0) ? rawDevLabels : ['💻 Kompüter / Noutbuk', '📱 Smartfon / Mobil', '📟 Planşet'];
                const devCounts = (rawDevCounts && rawDevCounts.length > 0 && rawDevCounts.some(v => v > 0)) ? rawDevCounts : [1, 0, 0];

                window.deviceChartInstance = new Chart(deviceCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: devLabels,
                        datasets: [{
                            data: devCounts,
                            backgroundColor: ['#C042F0', '#69E5FF', '#76FF5B', '#FFD13B', '#F43F5E'],
                            borderColor: '#16161C',
                            borderWidth: 3,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 350
                        },
                        cutout: '72%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#9CA3AF',
                                    font: { family: 'Plus Jakarta Sans', size: 12 },
                                    padding: 14,
                                    usePointStyle: true
                                }
                            }
                        }
                    }
                });
            } catch(err) {
                console.warn('Device chart error:', err);
            }
        };

        // Initialize Tab on Page Load
        function initCurrentDashboardTab() {
            try {
                const targetTab = (typeof window.detectInitialTab === 'function') 
                    ? window.detectInitialTab() 
                    : 'overview';

                window.switchDashboardTab(targetTab);
                if (typeof window.updateNotificationBadge === 'function') {
                    window.updateNotificationBadge();
                }

                // If starting on charts tab or overview, initialize charts immediately
                if (targetTab === 'charts' || targetTab === 'overview') {
                    setTimeout(() => {
                        if (typeof window.initOrUpdateTrafficChart === 'function') window.initOrUpdateTrafficChart();
                        if (typeof window.initOrUpdateDeviceChart === 'function') window.initOrUpdateDeviceChart();
                    }, 40);
                    setTimeout(() => {
                        if (typeof window.initOrUpdateTrafficChart === 'function') window.initOrUpdateTrafficChart();
                        if (typeof window.initOrUpdateDeviceChart === 'function') window.initOrUpdateDeviceChart();
                    }, 150);
                }
            } catch(e) {
                console.error('initCurrentDashboardTab error:', e);
            }
        }

        window.addEventListener('DOMContentLoaded', initCurrentDashboardTab);
        window.addEventListener('load', initCurrentDashboardTab);
        window.addEventListener('hashchange', () => {
            const hashTab = (window.location.hash || '').replace('#', '').trim();
            if (hashTab && window.tabTitles && window.tabTitles[hashTab]) {
                window.switchDashboardTab(hashTab);
            }
        });

        // Run immediately if ready
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            initCurrentDashboardTab();
        }
    </script>
</body>
</html>
