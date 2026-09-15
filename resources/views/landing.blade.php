<!DOCTYPE html>
<html lang="{{ $currentLocale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>AirSen (.com) — @lang('messages.hero_title')</title>
    <meta name="description" content="@lang('messages.hero_subtitle')">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="AirSen — Smart Carbon Monoxide Protection">
    <meta property="og:description" content="@lang('messages.hero_subtitle')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://airsen.com">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Google Fonts (Plus Jakarta Sans & Inter with 100% Full Azerbaijani Latin support) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,600&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Client Translations Dictionary -->
    <script>
        window.AIRSEN_TRANSLATIONS = @json($translations);
        window.GOOGLE_PLAY_CONFIG = {
            status: "{{ $googlePlayStatus }}",
            url: "{{ $googlePlayUrl }}"
        };
    </script>
</head>
<body>

    <!-- ====================================================================
         Header & Navigation (Glass Pill)
         ==================================================================== -->
    <header class="site-header">
        <nav class="nav-glass">
            <a href="#hero" class="brand-logo" aria-label="AirSen Home">
                <img src="{{ asset('images/logo.png') }}" alt="AirSen" class="site-logo-img">
            </a>

            <ul class="nav-links">
                <li><a href="#problem" class="nav-link" data-i18n="nav_problem">@lang('messages.nav_problem')</a></li>
                <li><a href="#how-it-works" class="nav-link" data-i18n="nav_how_it_works">@lang('messages.nav_how_it_works')</a></li>
                <li><a href="#features" class="nav-link" data-i18n="nav_features">@lang('messages.nav_features')</a></li>
                <li><a href="#pricing" class="nav-link" data-i18n="nav_plans">@lang('messages.nav_plans')</a></li>
                <li><a href="#team" class="nav-link" data-i18n="nav_team">@lang('messages.nav_team')</a></li>
                <li><a href="#investors" class="nav-link" data-i18n="nav_investors">@lang('messages.nav_investors')</a></li>
                <li><a href="#reviews" class="nav-link" data-i18n="nav_reviews">@lang('messages.nav_reviews')</a></li>
                <li><a href="#contact" class="nav-link" data-i18n="nav_contact">@lang('messages.nav_contact')</a></li>
            </ul>

            <div class="nav-actions">
                <!-- Language Switcher -->
                <div class="lang-switcher">
                    <button type="button" class="lang-btn js-lang-btn {{ $currentLocale === 'az' ? 'active' : '' }}" data-lang="az">AZ</button>
                    <button type="button" class="lang-btn js-lang-btn {{ $currentLocale === 'ru' ? 'active' : '' }}" data-lang="ru">RU</button>
                    <button type="button" class="lang-btn js-lang-btn {{ $currentLocale === 'en' ? 'active' : '' }}" data-lang="en">EN</button>
                </div>

                <!-- Secret Admin Shortcut -->
                <a href="{{ route('admin.index') }}" class="admin-lock-btn" title="@lang('messages.footer_admin_link')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </a>

                <!-- Mobile Hamburger Button -->
                <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Menyu">
                    <span class="hamburger-bar"></span>
                    <span class="hamburger-bar"></span>
                    <span class="hamburger-bar"></span>
                </button>
            </div>
        </nav>
    </header>

    <!-- Mobile Navigation Drawer Overlay -->
    <div class="mobile-nav-overlay" id="mobileNavOverlay">
        <div class="mobile-nav-drawer">
            <div class="mobile-drawer-header">
                <a href="#hero" class="brand-logo" id="mobileLogoLink">
                    <img src="{{ asset('images/logo.png') }}" alt="AirSen" class="site-logo-img" style="height: 38px;">
                </a>
                <button type="button" class="mobile-drawer-close" id="mobileDrawerClose" aria-label="Bağla">&times;</button>
            </div>
            <ul class="mobile-drawer-links">
                <li><a href="#problem" class="mobile-nav-link" data-i18n="nav_problem">@lang('messages.nav_problem')</a></li>
                <li><a href="#how-it-works" class="mobile-nav-link" data-i18n="nav_how_it_works">@lang('messages.nav_how_it_works')</a></li>
                <li><a href="#features" class="mobile-nav-link" data-i18n="nav_features">@lang('messages.nav_features')</a></li>
                <li><a href="#pricing" class="mobile-nav-link" data-i18n="nav_plans">@lang('messages.nav_plans')</a></li>
                <li><a href="#team" class="mobile-nav-link" data-i18n="nav_team">@lang('messages.nav_team')</a></li>
                <li><a href="#investors" class="mobile-nav-link" data-i18n="nav_investors">@lang('messages.nav_investors')</a></li>
                <li><a href="#reviews" class="mobile-nav-link" data-i18n="nav_reviews">@lang('messages.nav_reviews')</a></li>
                <li><a href="#contact" class="mobile-nav-link" data-i18n="nav_contact">@lang('messages.nav_contact')</a></li>
            </ul>
        </div>
    </div>


    <main class="airsen-container">

        <!-- ================================================================
             BLOCK 1: Hero Section (2/3 + 1        <!-- =========================================================================
             ORIGINAL HERO BACKUP (PRESERVED AS REQUESTED BY USER FOR REFERENCE)
             ========================================================================= -->
        <!--
        <section id="hero-backup" class="hero-grid" style="display: none;">
            <div class="bento-card hero-left-card">
                <div class="hero-logo-row">
                    <span class="hero-logo-text">AirSen</span>
                </div>
                <h1 class="hero-title">@lang('messages.hero_title')</h1>
                <p class="hero-subtitle">@lang('messages.hero_subtitle')</p>
            </div>
            <div class="bento-card hero-phone-card">
                <div class="phone-mockup-wrapper">...</div>
            </div>
        </section>
        -->

        <!-- =========================================================================
             BLOCK 1: ACTIVE HERO SECTION & PHONE STAGE (Bento Grid 2/3 + 1/3)
             ========================================================================= -->
        <section id="hero" class="hero-grid hero-stage-section">
            <!-- Left Card (2/3 width) -->
            <div class="bento-card hero-left-card">
                <div>
                    <div class="hero-logo-row">
                        <div class="brand-wordmark">
                            <img src="{{ asset('images/logo.png') }}" alt="AirSen" class="hero-brand-logo-img">
                        </div>
                        <div class="brutalist-badge badge-purple">
                            <span class="badge-pulse-dot"></span>
                            <span data-i18n="hero_badge">@lang('messages.hero_badge')</span>
                        </div>
                    </div>

                    <h1 class="hero-title" data-i18n="hero_title">
                        @lang('messages.hero_title')
                    </h1>

                    <p class="hero-subtitle" data-i18n="hero_subtitle">
                        @lang('messages.hero_subtitle')
                    </p>
                </div>

                <!-- Google Play CTA Button & Moderation Status -->
                <div class="cta-group">
                    <a href="{{ $googlePlayStatus === 'active' ? $googlePlayUrl : '#google-play' }}" 
                       class="btn-google-play js-google-play-btn {{ $googlePlayStatus === 'moderation' ? 'is-disabled-state' : '' }}"
                       data-status="{{ $googlePlayStatus }}"
                       target="{{ $googlePlayStatus === 'active' ? '_blank' : '_self' }}"
                       id="mainGooglePlayBtn"
                       title="Google Play">
                        <div class="gp-glow-layer"></div>
                        <img src="{{ asset('images/google-play-icon.svg') }}" alt="Google Play" class="gp-icon-img" width="26" height="26">
                        <span class="gp-single-label" data-i18n="cta_google_play">@lang('messages.cta_google_play')</span>
                    </a>

                    @if($googlePlayStatus === 'moderation')
                        <div class="moderation-tag" title="@lang('messages.cta_moderation_info')">
                            <span class="mod-live-dot"></span>
                            <span data-i18n="cta_moderation_badge">@lang('messages.cta_moderation_badge')</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Card (1/3 width): Spacious Full-Height Phone Stage -->
            <div class="bento-card hero-phone-card phone-stage card">
                <div class="phone-mockup-wrapper" id="phoneMockup">
                    <!-- Subtle Reflection Glass Sheen -->
                    <div class="phone-glass-sheen"></div>

                    <!-- Integrated iPhone Status Bar: Time + Dynamic Island + Icons (All on ONE row) -->
                    <div class="phone-sys-bar">
                        <span class="sys-time">09:41</span>
                        <div class="phone-island"></div>
                        <div class="sys-icons">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 3c-4.97 0-9 4.03-9 9 0 2.12.74 4.07 1.97 5.61L4.35 18.25c-.39.39-.39 1.02 0 1.41.39.39 1.02.39 1.41 0l.64-.64C7.93 19.26 9.88 20 12 20c4.97 0 9-4.03 9-9s-4.03-9-9-9z"/>
                            </svg>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 4C7.31 4 3.07 5.9 0 8.98L12 21 24 8.98C20.93 5.9 16.69 4 12 4z"/>
                            </svg>
                            <div class="battery-pill">
                                <div class="battery-level"></div>
                            </div>
                        </div>
                    </div>

                    <!-- App Bar -->
                    <div class="phone-app-bar">
                        <div class="phone-mode-switch">
                            <button type="button" class="phone-mode-btn active" id="modeHome">Home</button>
                            <button type="button" class="phone-mode-btn" id="modeCar">Car</button>
                        </div>
                    </div>

                    <!-- Location & Sensor Status Line -->
                    <div class="sensor-live-bar">
                        <span class="sensor-pulse-dot"></span>
                        <span class="sensor-info-text" id="sensorLiveText">QONAQ OTAĞI • 2.4 GHz CANLI BAĞLANTI</span>
                    </div>

                    <!-- 3 Symmetrical Concentric Rings with Fluid Liquid Wave Inner Core -->
                    <div class="co-dial-container">
                        <!-- Ring 3: Outer Pulse Ring -->
                        <div class="pulse-ring ring-three" id="coRingThree"></div>

                        <!-- Ring 2: Middle Pulse Ring -->
                        <div class="pulse-ring ring-two" id="coRingTwo"></div>

                        <!-- Ring 1: Inner Core with Moving Liquid Water Wave -->
                        <div class="pulse-ring inner-core" id="coInnerCore">
                            <div class="liquid-wave-layer" id="coLiquidWave"></div>
                            <div class="liquid-wave-mesh"></div>
                            <div class="inner-core-content">
                                <span class="co-core-label" id="coDialHeaderTag">CO</span>
                                <span class="co-core-value" id="coDialValue">12</span>
                                <span class="co-core-unit" data-i18n="mockup_ppm">@lang('messages.mockup_ppm')</span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Pill Badge (Clear of rings) -->
                    <div class="co-status-badge-wrapper">
                        <div class="co-status-badge" id="coStatusBadge" data-i18n="mockup_status_safe">
                            @lang('messages.mockup_status_safe')
                        </div>
                    </div>

                    <!-- Dynamic Action & Status Area (Constant Height Container) -->
                    <div class="phone-dynamic-card-area">
                        <!-- Normal State Card -->
                        <div id="mockupSafeBox" class="mockup-state-card active">
                            <div class="env-micro-stats">
                                <div class="env-stat-item">
                                    <span class="stat-lbl">STATUS</span>
                                    <span class="stat-val-green">TƏMİZ HAVA</span>
                                </div>
                                <div class="env-stat-divider"></div>
                                <div class="env-stat-item">
                                    <span class="stat-lbl">VENTİLYASİYA</span>
                                    <span class="stat-val-white">NORMAL</span>
                                </div>
                            </div>
                        </div>

                        <!-- Warning State Card -->
                        <div id="mockupWarnBox" class="mockup-state-card" style="display: none;">
                            <div class="warn-notice-box">
                                <div class="warn-notice-title">⚠️ YÜKSƏLƏN SƏVİYYƏ</div>
                                <div class="warn-notice-desc">Pəncərələri açın və otağı havalandırın</div>
                            </div>
                        </div>

                        <!-- Danger State Card (Voice Confirm + SOS in sleek 2-button grid) -->
                        <div id="mockupDangerBox" class="mockup-state-card" style="display: none;">
                            <div class="danger-action-grid">
                                <button type="button" id="mockupVoiceBtn" class="danger-btn btn-confirm-safe" title="Sistemi normal rejimə qaytar">
                                    <span>✓ Mən Yaxşıyam</span>
                                </button>
                                <a href="tel:112" id="mockupSosBtn" class="danger-btn btn-sos-call" title="112 Təcili Xidmət">
                                    <span>🚨 SOS (112)</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Family Network Bar -->
                    <div class="phone-family-box">
                        <div>
                            <div style="font-size: 11px; font-weight: 800; color: #FFF;" data-i18n="mockup_family_title">@lang('messages.mockup_family_title')</div>
                            <div style="font-size: 10px; color: var(--green-signal); font-weight: 700;" data-i18n="mockup_family_active">@lang('messages.mockup_family_active')</div>
                        </div>
                        <div class="phone-family-avatars">
                            <div class="avatar-mini" title="Turan">TS</div>
                            <div class="avatar-mini" title="Tamerlan">TS</div>
                            <div class="avatar-mini" title="Ailə">A</div>
                            <div class="avatar-mini">+1</div>
                        </div>
                    </div>
                </div>

                <!-- Simulator Trigger Controls -->
                <div class="phone-sim-controls">
                    <span style="font-size: 11px; font-weight: 700; color: var(--text-dim); text-align: center; letter-spacing: 0.04em;" data-i18n="mockup_interactive_hint">
                        @lang('messages.mockup_interactive_hint')
                    </span>
                    <div class="sim-btn-group">
                        <button type="button" class="sim-btn btn-safe active" id="simBtnSafe" data-i18n="mockup_simulate_safe">@lang('messages.mockup_simulate_safe')</button>
                        <button type="button" class="sim-btn btn-warn" id="simBtnWarn" data-i18n="mockup_simulate_warn">@lang('messages.mockup_simulate_warn')</button>
                        <button type="button" class="sim-btn btn-danger" id="simBtnDanger" data-i18n="mockup_simulate_danger">@lang('messages.mockup_simulate_danger')</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================================================================
             BLOCK 2: The Problem (Wide + Square Stat Card)
             ================================================================ -->
        <section id="problem" class="problem-grid">
            <!-- Card 1: The Problem (Wide Card) -->
            <div class="bento-card problem-card-wide card-glow-blue">
                <div>
                    <div class="brutalist-badge badge-blue" style="margin-bottom: 20px;">
                        <span data-i18n="problem_label">@lang('messages.problem_label')</span>
                    </div>
                    <h3 class="problem-heading" data-i18n="problem_title">
                        @lang('messages.problem_title')
                    </h3>
                </div>
                <p class="problem-description" data-i18n="problem_text">
                    @lang('messages.problem_text')
                </p>
            </div>

            <!-- Card 2: Stat (Vibrant Purple #C042F0 Square Card) -->
            <div class="bento-card problem-card-stat card-solid-purple">
                <div class="stat-top-group">
                    <div class="brutalist-badge badge-stat-tag">
                        <span data-i18n="stat_badge">@lang('messages.stat_badge')</span>
                    </div>
                    <div class="stat-huge-number" id="statCounter" data-target="28900">28 900</div>
                </div>
                <p class="stat-label-text" data-i18n="stat_label">
                    @lang('messages.stat_label')
                </p>
            </div>
        </section>

        <!-- ================================================================
             BLOCK 3: How It Works (3 Steps Flow + Detailed Cards)
             ================================================================ -->
        <section id="how-it-works">
            <div class="section-header">
                <h2 class="section-title" data-i18n="how_title">@lang('messages.how_title')</h2>
            </div>

            <!-- 3-Step Interactive Architecture Cards -->
            <div class="how-steps-flow-grid" style="margin-bottom: 24px;">
                <!-- Step 1: Connect -->
                <div class="how-step-card step-card-purple aos-item aos-fade-right aos-delay-1">
                    <div class="step-card-header">
                        <span class="step-badge step-badge-purple">01</span>
                        <div class="step-icon-wrap step-icon-purple">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4 class="step-title" data-i18n="how_step1_title">@lang('messages.how_step1_title')</h4>
                        <p class="step-desc" data-i18n="how_step1_desc">@lang('messages.how_step1_desc')</p>
                    </div>
                </div>

                <!-- Step 2: Sync -->
                <div class="how-step-card step-card-cyan aos-item aos-fade-up aos-delay-2">
                    <div class="step-card-header">
                        <span class="step-badge step-badge-cyan">02</span>
                        <div class="step-icon-wrap step-icon-cyan">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4 class="step-title" data-i18n="how_step2_title">@lang('messages.how_step2_title')</h4>
                        <p class="step-desc" data-i18n="how_step2_desc">@lang('messages.how_step2_desc')</p>
                    </div>
                </div>

                <!-- Step 3: Protect -->
                <div class="how-step-card step-card-green aos-item aos-fade-left aos-delay-3">
                    <div class="step-card-header">
                        <span class="step-badge step-badge-green">03</span>
                        <div class="step-icon-wrap step-icon-green">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4 class="step-title" data-i18n="how_step3_title">@lang('messages.how_step3_title')</h4>
                        <p class="step-desc" data-i18n="how_step3_desc">@lang('messages.how_step3_desc')</p>
                    </div>
                </div>
            </div>

            <div class="how-it-works-grid">
                <!-- Card 1: The Connection -->
                <div class="bento-card how-bento-card card-gradient-purple aos-item aos-zoom-in-up aos-delay-1">
                    <div class="how-card-icon icon-purple">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <h3 class="how-card-title" data-i18n="card1_title">@lang('messages.card1_title')</h3>
                    <p class="how-card-text" data-i18n="card1_text">@lang('messages.card1_text')</p>
                </div>

                <!-- Card 2: Danger Levels -->
                <div class="bento-card how-bento-card card-glow-green aos-item aos-zoom-in-up aos-delay-2">
                    <div class="how-card-icon icon-green">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                        </svg>
                    </div>
                    <h3 class="how-card-title" data-i18n="card2_title">@lang('messages.card2_title')</h3>
                    <p class="how-card-text" data-i18n="card2_text">@lang('messages.card2_text')</p>
                    <ul class="danger-levels-list">
                        <li class="danger-level-item item-green">
                            <span class="level-indicator-dot dot-green"></span>
                            <span data-i18n="card2_green">@lang('messages.card2_green')</span>
                        </li>
                        <li class="danger-level-item item-yellow">
                            <span class="level-indicator-dot dot-yellow"></span>
                            <span data-i18n="card2_yellow">@lang('messages.card2_yellow')</span>
                        </li>
                        <li class="danger-level-item item-red">
                            <span class="level-indicator-dot dot-red"></span>
                            <span data-i18n="card2_red">@lang('messages.card2_red')</span>
                        </li>
                    </ul>
                </div>

                <!-- Card 3: Voice Control -->
                <div class="bento-card how-bento-card card-glow-blue aos-item aos-zoom-in-up aos-delay-3">
                    <div class="how-card-icon icon-blue">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
                            <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                            <line x1="12" y1="19" x2="12" y2="22"/>
                        </svg>
                    </div>
                    <h3 class="how-card-title" data-i18n="card3_title">@lang('messages.card3_title')</h3>
                    <p class="how-card-text" data-i18n="card3_text">@lang('messages.card3_text')</p>
                </div>

                <!-- Card 4: The Escalation -->
                <div class="bento-card how-bento-card card-gradient-purple aos-item aos-zoom-in-up aos-delay-4">
                    <div class="how-card-icon icon-red">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="3"/>
                        </svg>
                    </div>
                    <h3 class="how-card-title" data-i18n="card4_title">@lang('messages.card4_title')</h3>
                    <p class="how-card-text" data-i18n="card4_text">@lang('messages.card4_text')</p>
                </div>
            </div>
        </section>

        <!-- ================================================================
             BLOCK 4: Technical Features & Advantages (3 Cards)
             ================================================================ -->
        <section id="features">
            <div class="section-header">
                <h2 class="section-title" data-i18n="feat_title">@lang('messages.feat_title')</h2>
            </div>

            <div class="features-grid">
                <!-- Feature 1: Locations -->
                <div class="bento-card feature-bento-card card-gradient-purple aos-item aos-feature-1 aos-delay-1">
                    <div class="feature-card-inner">
                        <div class="feature-pure-icon icon-feat-purple">
                            <svg class="feat-pure-svg feat-svg-purple" width="48" height="48" viewBox="0 0 40 40" fill="none">
                                <path class="feat-home-roof" d="M5 16L20 4l15 12v18a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V16z" stroke="#C042F0" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14 36V22h12v14" stroke="#C042F0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle class="feat-gps-ring" cx="29" cy="11" r="6.5" stroke="#69E5FF" stroke-width="1.5" stroke-dasharray="2.5 2.5"/>
                                <circle class="feat-gps-dot" cx="29" cy="11" r="2.8" fill="#69E5FF"/>
                            </svg>
                        </div>
                        <h3 class="feature-card-heading" data-i18n="feat1_title">@lang('messages.feat1_title')</h3>
                        <p class="feature-card-text" data-i18n="feat1_text">@lang('messages.feat1_text')</p>
                    </div>
                </div>

                <!-- Feature 2: Autonomy -->
                <div class="bento-card feature-bento-card card-glow-green aos-item aos-feature-2 aos-delay-2">
                    <div class="feature-card-inner">
                        <div class="feature-pure-icon icon-feat-green">
                            <svg class="feat-pure-svg feat-svg-green" width="48" height="48" viewBox="0 0 40 40" fill="none">
                                <path class="feat-shield" d="M20 4L7 9.5v10c0 8.5 5.6 16.5 13 18.5 7.4-2 13-10 13-18.5v-10L20 4z" stroke="#76FF5B" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <polygon class="feat-spark" points="21 10 12 21 19 21 17 30 28 19 21 19 23 10" fill="#76FF5B"/>
                            </svg>
                        </div>
                        <h3 class="feature-card-heading" data-i18n="feat2_title">@lang('messages.feat2_title')</h3>
                        <p class="feature-card-text" data-i18n="feat2_text">@lang('messages.feat2_text')</p>
                    </div>
                </div>

                <!-- Feature 3: Scalability -->
                <div class="bento-card feature-bento-card card-glow-blue aos-item aos-feature-3 aos-delay-3">
                    <div class="feature-card-inner">
                        <div class="feature-pure-icon icon-feat-blue">
                            <svg class="feat-pure-svg feat-svg-blue" width="48" height="48" viewBox="0 0 40 40" fill="none">
                                <rect class="feat-screen-main" x="4" y="6" width="22" height="17" rx="3" stroke="#69E5FF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <rect class="feat-screen-phone" x="22" y="15" width="14" height="20" rx="3" stroke="#69E5FF" stroke-width="2.2" fill="#15151F" stroke-linecap="round" stroke-linejoin="round"/>
                                <line x1="26" y1="31" x2="32" y2="31" stroke="#69E5FF" stroke-width="1.8" stroke-linecap="round"/>
                                <circle class="feat-sync-dot" cx="15" cy="14.5" r="2.5" fill="#69E5FF"/>
                            </svg>
                        </div>
                        <h3 class="feature-card-heading" data-i18n="feat3_title">@lang('messages.feat3_title')</h3>
                        <p class="feature-card-text" data-i18n="feat3_text">@lang('messages.feat3_text')</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================================================================
             BLOCK: Plans & Pricing (2 Bento Pricing Cards)
             ================================================================ -->
        <section id="pricing" class="pricing-section">
            <div class="section-header">
                <h2 class="section-title" data-i18n="pricing_title">@lang('messages.pricing_title')</h2>
            </div>

            <div class="pricing-grid">
                <!-- Card 1: BASIC PLAN (Free) -->
                <div class="bento-card pricing-card aos-item aos-pricing-card-1 aos-delay-1">
                    <div class="pricing-card-head">
                        <div class="pricing-plan-name">
                            <span data-i18n="plan_basic_title">@lang('messages.plan_basic_title')</span>
                            <span class="pricing-plan-badge" data-i18n="plan_basic_badge">@lang('messages.plan_basic_badge')</span>
                        </div>
                        <div class="pricing-price-wrap">
                            <span class="pricing-currency">$</span>
                            <span class="pricing-amount">0</span>
                            <span class="pricing-period" data-i18n="plan_basic_period">@lang('messages.plan_basic_period')</span>
                        </div>
                    </div>

                    <ul class="pricing-features-list">
                        <li class="pricing-feature-item">
                            <div class="pricing-feature-icon pricing-feature-icon-green">✓</div>
                            <div>
                                <div class="pricing-feature-title" data-i18n="plan_basic_f1_t">@lang('messages.plan_basic_f1_t')</div>
                                <div class="pricing-feature-desc" data-i18n="plan_basic_f1_d">@lang('messages.plan_basic_f1_d')</div>
                            </div>
                        </li>
                        <li class="pricing-feature-item">
                            <div class="pricing-feature-icon pricing-feature-icon-green">✓</div>
                            <div>
                                <div class="pricing-feature-title" data-i18n="plan_basic_f2_t">@lang('messages.plan_basic_f2_t')</div>
                                <div class="pricing-feature-desc" data-i18n="plan_basic_f2_d">@lang('messages.plan_basic_f2_d')</div>
                            </div>
                        </li>
                        <li class="pricing-feature-item">
                            <div class="pricing-feature-icon pricing-feature-icon-green">✓</div>
                            <div>
                                <div class="pricing-feature-title" data-i18n="plan_basic_f3_t">@lang('messages.plan_basic_f3_t')</div>
                                <div class="pricing-feature-desc" data-i18n="plan_basic_f3_d">@lang('messages.plan_basic_f3_d')</div>
                            </div>
                        </li>
                        <li class="pricing-feature-item">
                            <div class="pricing-feature-icon pricing-feature-icon-green">✓</div>
                            <div>
                                <div class="pricing-feature-title" data-i18n="plan_basic_f4_t">@lang('messages.plan_basic_f4_t')</div>
                                <div class="pricing-feature-desc" data-i18n="plan_basic_f4_d">@lang('messages.plan_basic_f4_d')</div>
                            </div>
                        </li>
                    </ul>

                    <div class="pricing-btn-wrap">
                        <a href="#hero" class="btn-pricing-action btn-pricing-free" data-i18n="plan_basic_btn">@lang('messages.plan_basic_btn')</a>
                    </div>
                </div>

                <!-- Card 2: PREMIUM SAFETY (Most Popular) -->
                <div class="bento-card pricing-card pricing-card-featured aos-item aos-pricing-card-2 aos-delay-2">
                    <div class="pricing-card-head">
                        <div class="pricing-plan-name">
                            <span data-i18n="plan_prem_title">@lang('messages.plan_prem_title')</span>
                            <span class="pricing-plan-badge badge-beta-pill" data-i18n="plan_prem_tag">@lang('messages.plan_prem_tag')</span>
                        </div>
                        <div class="pricing-price-wrap">
                            <span class="pricing-currency">$</span>
                            <span class="pricing-amount">5.99</span>
                            <span class="pricing-period" data-i18n="plan_prem_period">@lang('messages.plan_prem_period')</span>
                        </div>
                        <div class="pricing-sub-yearly" data-i18n="plan_prem_yearly">@lang('messages.plan_prem_yearly')</div>
                    </div>

                    <ul class="pricing-features-list">
                        <li class="pricing-feature-item">
                            <div class="pricing-feature-icon pricing-feature-icon-purple">★</div>
                            <div>
                                <div class="pricing-feature-title" data-i18n="plan_prem_f1_t">@lang('messages.plan_prem_f1_t')</div>
                                <div class="pricing-feature-desc" data-i18n="plan_prem_f1_d">@lang('messages.plan_prem_f1_d')</div>
                            </div>
                        </li>
                        <li class="pricing-feature-item">
                            <div class="pricing-feature-icon pricing-feature-icon-purple">★</div>
                            <div>
                                <div class="pricing-feature-title" data-i18n="plan_prem_f2_t">@lang('messages.plan_prem_f2_t')</div>
                                <div class="pricing-feature-desc" data-i18n="plan_prem_f2_d">@lang('messages.plan_prem_f2_d')</div>
                            </div>
                        </li>
                        <li class="pricing-feature-item">
                            <div class="pricing-feature-icon pricing-feature-icon-purple">★</div>
                            <div>
                                <div class="pricing-feature-title" data-i18n="plan_prem_f3_t">@lang('messages.plan_prem_f3_t')</div>
                                <div class="pricing-feature-desc" data-i18n="plan_prem_f3_d">@lang('messages.plan_prem_f3_d')</div>
                            </div>
                        </li>
                        <li class="pricing-feature-item">
                            <div class="pricing-feature-icon pricing-feature-icon-purple">★</div>
                            <div>
                                <div class="pricing-feature-title" data-i18n="plan_prem_f4_t">@lang('messages.plan_prem_f4_t')</div>
                                <div class="pricing-feature-desc" data-i18n="plan_prem_f4_d">@lang('messages.plan_prem_f4_d')</div>
                            </div>
                        </li>
                        <li class="pricing-feature-item">
                            <div class="pricing-feature-icon pricing-feature-icon-purple">★</div>
                            <div>
                                <div class="pricing-feature-title" data-i18n="plan_prem_f5_t">@lang('messages.plan_prem_f5_t')</div>
                                <div class="pricing-feature-desc" data-i18n="plan_prem_f5_d">@lang('messages.plan_prem_f5_d')</div>
                            </div>
                        </li>
                    </ul>

                    <div class="pricing-btn-wrap">
                        <button type="button" class="btn-pricing-action btn-pricing-featured js-beta-notify-btn" id="btnPricingNotify" data-i18n="plan_prem_btn">
                            @lang('messages.plan_prem_btn')
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================================================================
             BLOCK 5: The Team (Trust Block - 2 Cards)
             ================================================================ -->
        <section id="team" class="team-section">
            <div class="section-header">
                <h2 style="font-size: 36px;" data-i18n="team_title">@lang('messages.team_title')</h2>
            </div>

            <div class="team-grid">
                <!-- Founder & CTO: Turan Safarli -->
                <div class="bento-card team-card team-card-turan">
                    <div class="team-card-glow-bg"></div>
                    <div class="team-header-row">
                        <div class="team-avatar-wrapper avatar-purple">
                            <img src="{{ asset('images/1.jpg') }}" alt="Turan Safarli" class="team-avatar-img team-avatar-img-turan" loading="lazy">
                            <div class="avatar-neon-ring"></div>
                        </div>
                        <div class="team-identity">
                            <div class="team-info-name" data-i18n="team_turan_name">@lang('messages.team_turan_name')</div>
                            <div class="team-role-badge role-badge-purple" data-i18n="team_turan_role">@lang('messages.team_turan_role')</div>
                        </div>
                        <div class="team-quote-mark">“</div>
                    </div>
                    
                    <div class="team-quote-wrapper">
                        <p class="team-quote-text quote-purple" data-i18n="team_turan_desc">
                            @lang('messages.team_turan_desc')
                        </p>
                    </div>

                    <div class="team-card-footer">
                        <span class="team-tag tag-purple">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <span data-i18n="team_turan_tag">@lang('messages.team_turan_tag')</span>
                        </span>
                    </div>
                </div>

                <!-- Co-Founder & CEO: Tamerlan Safarli -->
                <div class="bento-card team-card team-card-tamerlan">
                    <div class="team-card-glow-bg"></div>
                    <div class="team-header-row">
                        <div class="team-avatar-wrapper avatar-cyan">
                            <img src="{{ asset('images/2.jpg') }}" alt="Tamerlan Safarli" class="team-avatar-img team-avatar-img-tamerlan" loading="lazy">
                            <div class="avatar-neon-ring"></div>
                        </div>
                        <div class="team-identity">
                            <div class="team-info-name" data-i18n="team_tamerlan_name">@lang('messages.team_tamerlan_name')</div>
                            <div class="team-role-badge role-badge-cyan" data-i18n="team_tamerlan_role">@lang('messages.team_tamerlan_role')</div>
                        </div>
                        <div class="team-quote-mark">“</div>
                    </div>
                    
                    <div class="team-quote-wrapper">
                        <p class="team-quote-text quote-cyan" data-i18n="team_tamerlan_desc">
                            @lang('messages.team_tamerlan_desc')
                        </p>
                    </div>

                    <div class="team-card-footer">
                        <span class="team-tag tag-cyan">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            <span data-i18n="team_tamerlan_tag">@lang('messages.team_tamerlan_tag')</span>
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================================================================
             BLOCK 6: Project Status & Investors (2 Arch & Dome Bento Cards)
             ================================================================ -->
        <section id="investors" class="investors-section">
            <div class="section-header">
                <div class="brutalist-badge badge-purple">
                    <span data-i18n="investors_label">@lang('messages.investors_label')</span>
                </div>
                <h2 style="font-size: 36px;" data-i18n="investors_title">@lang('messages.investors_title')</h2>
            </div>

            <div class="status-investors-grid">
                <!-- Card 1: Investors Opportunity Card -->
                <div class="investor-arch-card card-arch-purple">
                    <div class="arch-pic-box">
                        <div class="arch-icon-wrapper arch-icon-purple">
                            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                        </div>
                    </div>
                    <div class="arch-card-content">
                        <div class="brutalist-badge badge-purple" style="margin-bottom: 12px; display: inline-flex;">
                            <span data-i18n="investors_badge">@lang('messages.investors_badge')</span>
                        </div>
                        <h3 class="arch-title" data-i18n="investors_card_title">@lang('messages.investors_card_title')</h3>
                        <p class="arch-text" data-i18n="investors_card_text">@lang('messages.investors_card_text')</p>
                    </div>
                    <div class="arch-card-footer">
                        <span class="arch-tag tag-purple">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                            <span data-i18n="investors_tag">@lang('messages.investors_tag')</span>
                        </span>
                    </div>
                </div>

                <!-- Card 2: Status & Roadmap Card -->
                <div class="investor-arch-card card-arch-green">
                    <div class="arch-pic-box">
                        <div class="arch-icon-wrapper arch-icon-green">
                            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="16" height="20" x="4" y="2" rx="2"/>
                                <line x1="12" x2="12.01" y1="18" y2="18"/>
                                <path d="M8 6h8"/>
                            </svg>
                        </div>
                    </div>
                    <div class="arch-card-content">
                        <div class="brutalist-badge badge-green" style="margin-bottom: 12px; display: inline-flex;">
                            <span class="badge-pulse-dot"></span>
                            <span data-i18n="status_badge">@lang('messages.status_badge')</span>
                        </div>
                        <h3 class="arch-title" data-i18n="status_card_title">@lang('messages.status_card_title')</h3>
                        <p class="arch-text" data-i18n="status_card_text">@lang('messages.status_card_text')</p>
                    </div>
                    <div class="arch-card-footer">
                        <span class="arch-tag tag-green">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            <span data-i18n="status_tag">@lang('messages.status_tag')</span>
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================================================================
             BLOCK 7 / Function №2: Public Comments & Reviews Wall (Auto-Glide Slider)
             ================================================================ -->
        <section id="reviews" class="reviews-section">
            <div class="reviews-top-bar">
                <div>
                    <h2 style="font-size: 36px; margin: 0;" data-i18n="reviews_title">@lang('messages.reviews_title')</h2>
                </div>

                <button type="button" class="btn-open-review" id="btnOpenReviewModal">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <span data-i18n="btn_leave_review">@lang('messages.btn_leave_review')</span>
                </button>
            </div>

            <!-- Empty Reviews State -->
            <div class="reviews-empty-state" id="reviewsEmptyState" style="{{ count($approvedReviews) > 0 ? 'display: none;' : 'display: flex;' }}">
                <div class="reviews-empty-icon">🛡️</div>
                <h4 class="reviews-empty-title" data-i18n="reviews_empty_title">@lang('messages.reviews_empty_title')</h4>
                <p class="reviews-empty-desc" data-i18n="reviews_empty_desc">@lang('messages.reviews_empty_desc')</p>
                <button type="button" class="btn-open-review" onclick="document.getElementById('btnOpenReviewModal').click();" style="margin-top: 10px;">
                    <span data-i18n="btn_leave_review">@lang('messages.btn_leave_review')</span>
                </button>
            </div>

            <div class="reviews-slider-container" style="{{ count($approvedReviews) > 0 ? '' : 'display: none;' }}">
                <div class="reviews-slider-viewport" id="reviewsSliderViewport">
                    <div class="reviews-slider-track" id="reviewsSliderTrack">
                        @foreach($approvedReviews as $review)
                            <div class="review-card slider-card">
                                <div class="review-card-top">
                                    <div class="review-stars-row">
                                        <div class="review-stars" aria-label="{{ $review->rating }} / 5">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($review->rating >= $i)
                                                    <span class="star-filled">★</span>
                                                @elseif($review->rating >= ($i - 0.5))
                                                    <span class="star-half">★</span>
                                                @else
                                                    <span class="star-empty">★</span>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="rating-num-badge">{{ number_format($review->rating, 1) }}</span>
                                    </div>
                                    <p class="review-comment">{{ $review->comment }}</p>
                                </div>
                                <div class="review-author">
                                    <div class="review-author-avatar">{{ strtoupper(substr($review->name, 0, 1)) }}</div>
                                    <div class="review-author-name">{{ $review->name }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Bottom Controls: Navigation Arrows, Uniform Dots & Live Progress Bar (Only when > 3 reviews) -->
                @if(count($approvedReviews) > 3)
                <div class="slider-bottom-bar slider-footer-controls" id="reviewsSliderControls">
                    <div class="slider-controls-row">
                        <button type="button" class="slider-arrow-btn" id="btnSliderPrev" aria-label="Əvvəlki">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
                        </button>

                        <div class="slider-dots-box" id="sliderDotsBox"></div>

                        <button type="button" class="slider-arrow-btn" id="btnSliderNext" aria-label="Növbəti">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                    </div>

                    <div class="slider-progress-track">
                        <div class="slider-progress-bar" id="sliderProgressBar"></div>
                    </div>
                </div>
                @endif
            </div>
        </section>

        <!-- ================================================================
             BLOCK 8: CONTACT US (2-Card Bento Grid Layout)
             ================================================================ -->
        <section id="contact" class="contact-section">
            <div class="section-header">
                <h2 class="section-title" data-i18n="contact_title">@lang('messages.contact_title')</h2>
            </div>

            <div class="contact-grid">
                <!-- Card 1: Direct Reach & Communication -->
                <div class="bento-card contact-bento-card card-gradient-purple">
                    <div class="contact-card-head">
                        <div class="brutalist-badge badge-purple" style="margin-bottom: 14px; display: inline-flex;">
                            <span data-i18n="contact_badge_direct">@lang('messages.contact_badge_direct')</span>
                        </div>
                        <h3 class="contact-card-heading" data-i18n="contact_direct_title">@lang('messages.contact_direct_title')</h3>
                        <p class="contact-card-sub" data-i18n="contact_direct_sub">@lang('messages.contact_direct_sub')</p>
                    </div>

                    <div class="contact-action-box">
                        <a href="mailto:airsen.info@gmail.com" class="contact-email-pill" id="mainContactEmailLink" title="Email AirSen">
                            <div class="email-pill-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                </svg>
                            </div>
                            <span class="email-pill-text">airsen.info@gmail.com</span>
                        </a>
                    </div>
                </div>

                <!-- Card 2: Distributed Global Network / Hubs -->
                <div class="bento-card contact-bento-card card-glow-blue">
                    <div class="contact-card-head">
                        <div class="brutalist-badge badge-blue" style="margin-bottom: 14px; display: inline-flex;">
                            <span data-i18n="contact_badge_global">@lang('messages.contact_badge_global')</span>
                        </div>
                        <h3 class="contact-card-heading" data-i18n="contact_global_title">@lang('messages.contact_global_title')</h3>
                    </div>

                    <div class="contact-hubs-list">
                        <!-- Hub 1: Baku -->
                        <div class="contact-hub-item">
                            <div class="hub-item-icon hub-icon-cyan">📍</div>
                            <div class="hub-item-info">
                                <div class="hub-item-title">Baku Hub</div>
                                <div class="hub-item-loc">Baku, Azerbaijan</div>
                                <div class="hub-item-desc" data-i18n="contact_hub_baku_desc">@lang('messages.contact_hub_baku_desc')</div>
                            </div>
                        </div>

                        <!-- Hub 2: Stanford -->
                        <div class="contact-hub-item">
                            <div class="hub-item-icon hub-icon-purple">🎓</div>
                            <div class="hub-item-info">
                                <div class="hub-item-title">Stanford Hub</div>
                                <div class="hub-item-loc">Stanford, CA / USA</div>
                                <div class="hub-item-desc" data-i18n="contact_hub_stanford_desc">@lang('messages.contact_hub_stanford_desc')</div>
                            </div>
                        </div>
                    </div>

                    <div class="contact-team-tag">
                        <span class="hub-pulse-dot"></span>
                        <span data-i18n="contact_location_line">@lang('messages.contact_location_line')</span>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- ====================================================================
         Beta Notification Pop-up Modal (Early Access & 3 Months Free Gift)
         ==================================================================== -->
    <div class="beta-modal" id="betaModal" role="dialog" aria-modal="true" aria-labelledby="betaModalTitle">
        <div class="beta-modal-backdrop"></div>
        <div class="beta-modal-container">
            <button type="button" class="beta-modal-close" id="betaModalClose" aria-label="Bağla">&times;</button>
            
            <div class="beta-badge" data-i18n="beta_modal_badge">
                <span class="mod-live-dot"></span>
                <span>@lang('messages.beta_modal_badge')</span>
            </div>

            <h3 class="beta-modal-title" id="betaModalTitle" data-i18n="beta_modal_title">
                @lang('messages.beta_modal_title')
            </h3>
            
            <p class="beta-modal-desc" data-i18n="beta_modal_desc">
                @lang('messages.beta_modal_desc')
            </p>

            <div class="beta-perk-box">
                <span class="beta-perk-icon">🎁</span>
                <span class="beta-perk-text" data-i18n="beta_perk_text">
                    @lang('messages.beta_perk_text')
                </span>
            </div>

            <form class="beta-form" id="betaNotifyForm">
                <div class="beta-input-group">
                    <input type="email" id="betaEmailInput" class="beta-input" required data-i18n="beta_input_placeholder" placeholder="@lang('messages.beta_input_placeholder')">
                </div>
                <button type="submit" class="btn-beta-submit">
                    <span data-i18n="beta_submit_btn">@lang('messages.beta_submit_btn')</span> →
                </button>
            </form>

            <div class="beta-success-msg" id="betaSuccessMsg" data-i18n="beta_success_text">
                @lang('messages.beta_success_text')
            </div>
        </div>
    </div>

    <!-- ====================================================================
         Review Submission Modal (With Mandatory Premoderation)
         ==================================================================== -->
    <div class="modal-backdrop" id="reviewModal">
        <div class="modal-content">
            <div class="modal-header-row">
                <h3 class="modal-title" data-i18n="modal_review_title">@lang('messages.modal_review_title')</h3>
                <button type="button" class="modal-close-btn" id="reviewModalClose" aria-label="Bağla">&times;</button>
            </div>

            <form id="reviewForm">
                <div class="form-group">
                    <label class="form-label" data-i18n="modal_name">@lang('messages.modal_name')</label>
                    <input type="text" id="reviewNameInput" class="form-input" maxlength="100" required data-i18n="modal_name_placeholder" placeholder="@lang('messages.modal_name_placeholder')">
                </div>

                <div class="form-group">
                    <div class="form-label-row">
                        <label class="form-label" style="margin-bottom: 0;" data-i18n="modal_rating_label">@lang('messages.modal_rating_label')</label>
                        <span class="live-score-pill" id="liveScorePill">5.0 / 5.0</span>
                    </div>
                    <div class="rating-picker-wrapper">
                        <div class="rating-picker" id="starPicker">
                            <span class="star-pick active" data-index="1">★</span>
                            <span class="star-pick active" data-index="2">★</span>
                            <span class="star-pick active" data-index="3">★</span>
                            <span class="star-pick active" data-index="4">★</span>
                            <span class="star-pick active" data-index="5">★</span>
                        </div>
                        <div class="live-rating-text" id="liveRatingText">{{ $currentLocale === 'ru' ? '5.0 — Отлично' : ($currentLocale === 'en' ? '5.0 — Excellent' : '5.0 — Möhtəşəm') }}</div>
                    </div>
                    <input type="hidden" id="reviewRatingInput" value="5">
                </div>

                <div class="form-group">
                    <div class="form-label-row">
                        <label class="form-label" style="margin-bottom: 0;" data-i18n="modal_comment">@lang('messages.modal_comment')</label>
                        <span class="char-count-pill" id="reviewCharCountWrap">
                            <span id="reviewCharCount">0</span> / 1000
                        </span>
                    </div>
                    <textarea id="reviewCommentInput" class="form-textarea" rows="4" maxlength="1000" required data-i18n="modal_comment_placeholder" placeholder="@lang('messages.modal_comment_placeholder')"></textarea>
                    <div class="char-limit-warning" id="charLimitWarning" style="display: none;">
                        <span id="charLimitWarningText">Maksimum 1000 simvol həddinə çatdınız.</span>
                    </div>
                </div>

                <button type="submit" class="btn-open-review" style="width: 100%; justify-content: center;">
                    <span data-i18n="modal_submit">@lang('messages.modal_submit')</span>
                </button>
            </form>
        </div>
    </div>

    <!-- ====================================================================
         Google Play Moderation Info Modal
         ==================================================================== -->
    <div class="modal-backdrop" id="gpModerationModal">
        <div class="modal-content" style="text-align: center;">
            <button type="button" class="modal-close-btn" id="gpModalClose">&times;</button>
            <div style="font-size: 40px; margin-bottom: 14px;">🚀</div>
            <h3 class="modal-title" style="margin-bottom: 12px;" data-i18n="cta_moderation_badge">@lang('messages.cta_moderation_badge')</h3>
            <p style="color: var(--text-muted); font-size: 15px; line-height: 1.6; margin-bottom: 24px;" data-i18n="cta_moderation_info">
                @lang('messages.cta_moderation_info')
            </p>
            <button type="button" class="btn-open-review" style="margin: 0 auto;" onclick="document.getElementById('gpModerationModal').classList.remove('is-open')">
                <span data-i18n="modal_close">@lang('messages.modal_close')</span>
            </button>
        </div>
    </div>

    <!-- ====================================================================
         Footer
         ==================================================================== -->
    <footer class="site-footer">
        <div class="airsen-container footer-inner">
            <a href="#hero" class="brand-logo footer-logo-link" aria-label="AirSen Home">
                <img src="{{ asset('images/logo.png') }}" alt="AirSen" class="footer-logo-img">
            </a>

            <div class="footer-credits">
                &copy; {{ date('Y') }} AirSen.com. <span data-i18n="footer_copyright">@lang('messages.footer_copyright')</span>
            </div>

            <a href="{{ route('admin.index') }}" class="footer-admin-link" data-i18n="footer_admin_link">
                <span>🔒</span> <span>@lang('messages.footer_admin_link')</span>
            </a>
        </div>
    </footer>

    <!-- ====================================================================
         AirSen Back To Top Button
         ==================================================================== -->
    <button type="button" class="airsen-back-to-top" id="bttMainBtn" aria-label="Yuxarı Qayıt" title="Yuxarı Qayıt">
        <svg class="btt-arrow-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 19V5M5 12l7-7 7 7"/>
        </svg>
    </button>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}?v={{ time() }}"></script>
</body>
</html>
