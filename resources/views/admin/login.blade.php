<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirSen — İdarəetmə Sisteminə Giriş</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Google Fonts (Plus Jakarta Sans & Inter with Full Latin Extended Support) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    
    <!-- SweetAlert2 -->
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
</head>
<body class="admin-login-body">
    <div class="login-ambient-orb orb-purple"></div>
    <div class="login-ambient-orb orb-cyan"></div>

    <div class="admin-login-container">
        <div class="admin-login-card">
            <!-- Brand Header -->
            <div class="login-brand-header">
                <a href="{{ route('landing.index') }}" class="login-logo-link" title="AirSen Ana Səhifə">
                    <img src="{{ asset('images/logo.png') }}" alt="AirSen" class="login-logo-img">
                </a>
                
                <div class="login-badge-pill">
                    <span class="badge-dot-live"></span>
                    <span>İDARƏETMƏ PANELİ & ANALİTİKA</span>
                </div>

                <h1 class="login-title">Xoş Gəlmisiniz</h1>
                <p class="login-subtitle">Platformanı idarə etmək üçün hesabınıza daxil olun.</p>
            </div>

            <!-- Form: Email & Password -->
            <form action="{{ route('admin.login') }}" method="POST" id="formEmailLogin" class="login-form" style="margin-top: 24px;">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="loginEmail">E-poçt ünvanı</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <input type="email" name="email" id="loginEmail" class="form-input" placeholder="admin@airsen.com" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="loginPassword">Şifrə</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input type="password" name="password" id="loginPassword" class="form-input" placeholder="••••••••" required>
                        <button type="button" class="password-toggle-btn" id="togglePasswordBtn" aria-label="Şifrəni göstər">
                            <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div class="login-options-row">
                    <label class="custom-checkbox-label">
                        <input type="checkbox" name="remember" value="1">
                        <span>Məni xatırla</span>
                    </label>
                </div>

                <button type="submit" class="btn-login-submit">
                    <span>Sistemə Daxil Ol</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </form>

            <!-- Bottom Back Link -->
            <div class="login-footer">
                <a href="{{ route('landing.index') }}" class="back-home-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
                    <span>Əsas Sayta Qayıt</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Interactive Script -->
    <script>
        // Password Toggle
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('loginPassword');
        toggleBtn?.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            if (isPassword) {
                toggleBtn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>`;
                toggleBtn.style.color = 'var(--purple-main)';
            } else {
                toggleBtn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>`;
                toggleBtn.style.color = 'var(--text-dim)';
            }
        });

        // Server-side Flash Alerts
        @if(session('error'))
            try {
                if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Giriş Uğursuzdur',
                        text: {!! json_encode(session('error')) !!},
                        background: '#16161C',
                        color: '#FFFFFF',
                        confirmButtonColor: '#dc3545',
                        borderRadius: '18px'
                    });
                } else {
                    alert({!! json_encode(session('error')) !!});
                }
            } catch(e) {
                console.error(e);
            }
        @endif
    </script>
</body>
</html>
