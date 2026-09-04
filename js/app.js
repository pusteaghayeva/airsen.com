/**
 * AirSen (.com) - Landing Page Interactive Script
 * - Real-time Audio/Visual CO Indicator Simulator
 * - Multi-language instant DOM switcher
 * - Hidden Analytics tracking
 * - Review submission with pre-moderation
 * - Google Play CTA moderation trigger
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Initialize Analytics Tracking
  trackEvent('page_view', {
    language: document.documentElement.lang || 'az',
    page_url: window.location.pathname
  });

  // 2. Animated Counter for 28 900 Stat
  initStatCounter();

  // 3. Smartphone Mockup CO Simulator
  initCOSimulator();

  // 4. Review Modal & Rating Picker
  initReviewModal();

  // 5. Google Play Button Moderation Handler
  initGooglePlayCTA();

  // 6. Language Switcher Interaction
  initLanguageSwitcher();

  // 7. Review Display Variants (Slider & Load More)
  initReviewVariants();

  // 8. Scroll Reveal Animations
  initScrollReveal();

  // 9. Dynamic Sticky Header
  initStickyHeader();

  // 10. Interactive Back To Top Widget
  initBackToTop();

  // 11. Mobile Navigation Drawer
  initMobileNav();

  // 12. Smooth Anchor Navigation & Active Scrollspy
  initScrollspy();
});

/* ==========================================================================
   Analytics Helper (Hidden Visit Counter)
   ========================================================================== */
function trackEvent(eventType, extraData = {}) {
  try {
    const payload = {
      event_type: eventType,
      language: extraData.language || document.documentElement.lang || 'az',
      page_url: extraData.page_url || window.location.pathname,
      country: Intl.DateTimeFormat().resolvedOptions().timeZone || 'Unknown'
    };

    fetch('/api/track', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify(payload)
    }).catch(err => console.debug('Analytics debug:', err));
  } catch (e) {
    // Non-blocking
  }
}

/* ==========================================================================
   Animated Stat Counter (28 900)
   ========================================================================== */
function initStatCounter() {
  const statEl = document.getElementById('statCounter');
  if (!statEl) return;

  const target = 28900;
  let current = 0;
  const duration = 2000;
  const stepTime = 20;
  const totalSteps = duration / stepTime;
  const increment = target / totalSteps;

  const observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting) {
      const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
          current = target;
          clearInterval(timer);
        }
        statEl.textContent = Math.floor(current).toLocaleString('en-US').replace(',', ' ');
      }, stepTime);
      observer.disconnect();
    }
  }, { threshold: 0.2 });

  observer.observe(statEl);
}

/* ==========================================================================
   Interactive Smartphone Mockup CO Simulator
   ========================================================================== */
let audioCtx = null;
let sirenOscillator = null;
let sirenInterval = null;

function initCOSimulator() {
  const ringThree = document.getElementById('coRingThree');
  const ringTwo = document.getElementById('coRingTwo');
  const innerCore = document.getElementById('coInnerCore');
  const liquidWave = document.getElementById('coLiquidWave');
  const dialValue = document.getElementById('coDialValue');
  const statusBadge = document.getElementById('coStatusBadge');
  const headerTag = document.getElementById('coDialHeaderTag');

  const safeBox = document.getElementById('mockupSafeBox');
  const warnBox = document.getElementById('mockupWarnBox');
  const dangerBox = document.getElementById('mockupDangerBox');

  const voiceBox = document.getElementById('mockupVoiceBox');
  const sosBox = document.getElementById('mockupSosBox');

  const btnSafe = document.getElementById('simBtnSafe');
  const btnWarn = document.getElementById('simBtnWarn');
  const btnDanger = document.getElementById('simBtnDanger');
  const simButtons = [btnSafe, btnWarn, btnDanger];

  const modeHome = document.getElementById('modeHome');
  const modeCar = document.getElementById('modeCar');

  if (!innerCore || !dialValue || !statusBadge) return;

  function setSimState(state, ppm, labelKey, defaultLabel) {
    // Remove active classes
    simButtons.forEach(btn => btn?.classList.remove('active'));
    statusBadge.className = 'co-status-badge';

    // Animate dial value
    animateValue(dialValue, parseInt(dialValue.textContent) || 0, ppm, 400);

    // Get current translation for the status
    const lang = document.documentElement.lang || 'az';
    const dict = window.AIRSEN_TRANSLATIONS?.[lang] || {};
    const text = dict[labelKey] || defaultLabel;
    statusBadge.textContent = text;

    // State styling, 3 Rings, Liquid Wave & Audio
    if (state === 'safe') {
      btnSafe?.classList.add('active');

      if (ringThree) ringThree.style.borderColor = 'rgba(118, 255, 91, 0.18)';
      if (ringTwo) ringTwo.style.borderColor = 'rgba(118, 255, 91, 0.38)';
      if (innerCore) {
        innerCore.style.borderColor = 'var(--green-signal)';
        innerCore.style.boxShadow = '0 0 25px rgba(118, 255, 91, 0.4), inset 0 0 15px rgba(118, 255, 91, 0.2)';
      }
      if (liquidWave) {
        liquidWave.style.background = 'radial-gradient(circle at 35% 35%, rgba(118, 255, 91, 0.45) 0%, rgba(18, 55, 22, 0.95) 45%, #0C180E 100%)';
      }
      if (headerTag) headerTag.style.color = 'var(--green-signal)';
      
      if (safeBox) safeBox.style.display = 'block';
      if (warnBox) warnBox.style.display = 'none';
      if (dangerBox) dangerBox.style.display = 'none';

      stopSiren();
    } else if (state === 'warn') {
      btnWarn?.classList.add('active');
      statusBadge.classList.add('warning-state');

      if (ringThree) ringThree.style.borderColor = 'rgba(255, 209, 59, 0.22)';
      if (ringTwo) ringTwo.style.borderColor = 'rgba(255, 209, 59, 0.48)';
      if (innerCore) {
        innerCore.style.borderColor = 'var(--yellow-warn)';
        innerCore.style.boxShadow = '0 0 30px rgba(255, 209, 59, 0.5), inset 0 0 18px rgba(255, 209, 59, 0.25)';
      }
      if (liquidWave) {
        liquidWave.style.background = 'radial-gradient(circle at 35% 35%, rgba(255, 209, 59, 0.5) 0%, rgba(60, 48, 12, 0.95) 45%, #181305 100%)';
      }
      if (headerTag) headerTag.style.color = 'var(--yellow-warn)';

      if (safeBox) safeBox.style.display = 'none';
      if (warnBox) warnBox.style.display = 'block';
      if (dangerBox) dangerBox.style.display = 'none';

      playWarningBeep();
    } else if (state === 'danger') {
      btnDanger?.classList.add('active');
      statusBadge.classList.add('danger-state');

      if (ringThree) ringThree.style.borderColor = 'rgba(220, 53, 69, 0.4)';
      if (ringTwo) ringTwo.style.borderColor = 'rgba(220, 53, 69, 0.75)';
      if (innerCore) {
        innerCore.style.borderColor = '#DC3545';
        innerCore.style.boxShadow = '0 0 45px rgba(220, 53, 69, 0.8), inset 0 0 22px rgba(220, 53, 69, 0.45)';
      }
      if (liquidWave) {
        liquidWave.style.background = 'radial-gradient(circle at 35% 35%, rgba(220, 53, 69, 0.85) 0%, rgba(130, 20, 30, 0.98) 45%, #250206 100%)';
      }
      if (headerTag) headerTag.style.color = '#DC3545';

      if (safeBox) safeBox.style.display = 'none';
      if (warnBox) warnBox.style.display = 'none';
      if (dangerBox) dangerBox.style.display = 'block';

      startSiren();
    }

    trackEvent('simulator_interaction', { state, ppm });
  }

  btnSafe?.addEventListener('click', () => setSimState('safe', 12, 'mockup_status_safe', '0–30 PPM • TƏHLÜKƏSİZ'));
  btnWarn?.addEventListener('click', () => setSimState('warn', 42, 'mockup_status_warning', '31–69 PPM • XƏBƏRDARLIQ'));
  btnDanger?.addEventListener('click', () => setSimState('danger', 110, 'mockup_status_danger', '70+ PPM • DƏM QAZI TƏHLÜKƏSİ'));

  // Home vs Car Sensor Mode toggle
  modeHome?.addEventListener('click', () => {
    modeHome.classList.add('active');
    modeCar.classList.remove('active');
  });

  // 3D Parallax Tilt Effect on Mouse Move
  const phoneCard = document.querySelector('.hero-phone-card');
  const phoneWrapper = document.querySelector('.phone-mockup-wrapper');

  if (phoneCard && phoneWrapper) {
    phoneCard.addEventListener('mousemove', (e) => {
      const rect = phoneCard.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      const centerX = rect.width / 2;
      const centerY = rect.height / 2;

      const rotateX = ((y - centerY) / centerY) * -12;
      const rotateY = ((x - centerX) / centerX) * 12;

      phoneWrapper.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
    });

    phoneCard.addEventListener('mouseleave', () => {
      phoneWrapper.style.transform = '';
    });
  }

  modeCar?.addEventListener('click', () => {
    modeCar.classList.add('active');
    modeHome?.classList.remove('active');
  });

  // Voice confirmation button click ("Mən Yaxşıyam")
  const voiceConfirmBtn = document.getElementById('mockupVoiceBtn');
  voiceConfirmBtn?.addEventListener('click', () => {
    stopSiren();

    const lang = document.documentElement.lang || 'az';
    const isRu = lang === 'ru';
    const isEn = lang === 'en';

    const safeTitle = isRu ? 'БЕЗОПАСНОСТЬ ПОДТВЕРЖДЕНА' : (isEn ? 'SAFETY CONFIRMED' : 'TƏHLÜKƏSİZLİK TƏSDİQLƏNDİ');
    const safeDesc = isRu
      ? 'Голосовое и ручное подтверждение принято. Сирена отключена, родственники уведомлены, что вы в безопасности.'
      : (isEn 
        ? 'Voice and manual check verified. Siren deactivated, emergency alert canceled, and family notified.'
        : 'Səs və düymə identifikasiyası qeydə alındı. Siren dayandırıldı, ailəyə təhlükəsizlik statusu ötürüldü.');

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: `<div style="font-family: var(--font-display, sans-serif); font-weight: 800; font-size: 20px; color: #76FF5B; display: flex; align-items: center; justify-content: center; gap: 8px;">
                  <span style="font-size: 22px;">✓</span>
                  <span>${safeTitle}</span>
                </div>`,
        html: `<div style="background: rgba(118, 255, 91, 0.08); border: 1px solid rgba(118, 255, 91, 0.25); border-radius: 14px; padding: 14px; margin: 12px 0 4px 0; color: #E5E7EB; font-size: 13px; line-height: 1.5; text-align: center;">
                 <p style="margin: 0; font-weight: 600;">${safeDesc}</p>
               </div>`,
        background: '#121218',
        color: '#FFFFFF',
        timer: 2600,
        timerProgressBar: true,
        showConfirmButton: false,
        customClass: {
          popup: 'airsen-swal-popup'
        }
      });
    }

    setSimState('safe', 12, 'mockup_status_safe', '0–30 PPM • TƏHLÜKƏSİZ');
  });

  // SOS Emergency button click
  const sosBtn = document.getElementById('mockupSosBtn');
  sosBtn?.addEventListener('click', (e) => {
    e.preventDefault();

    const lang = document.documentElement.lang || 'az';
    const isRu = lang === 'ru';
    const isEn = lang === 'en';

    const sosTitle = isRu ? 'ЭКСТРЕННЫЙ ПРОТОКОЛ АКТИВИРОВАН' : (isEn ? 'EMERGENCY PROTOCOL ACTIVE' : 'TƏCİLİ PROTOKOL AKTİVLƏŞDİ');
    const gpsText = isRu ? 'Живые GPS координаты' : (isEn ? 'Live GPS Location' : 'Canlı GPS Koordinatı');
    const familyText = isRu ? 'Семье отправлено экстренное push-уведомление и адрес.' : (isEn ? 'Emergency notification and live location sent to all family members.' : '4 ailə üzvünə təcili bildiriş və dəqiq ünvan göndərildi.');
    const serviceText = isRu ? 'Инициирован прямой вызов 112 (МЧС) и 103 (Скорая).' : (isEn ? 'Direct emergency call initiated to 911 / 112 Services.' : '112 (FHN) və 103 (Təcili Yardım) ilə birbaşa əlaqə yaradılır.');
    const callBtnText = isRu ? '📞 Позвонить 112' : (isEn ? '📞 Call 112' : '📞 112 Təcili Zəng');
    const closeBtnText = isRu ? '✕ Закрыть / Тест' : (isEn ? '✕ Close / Test' : '✕ Bağla / Sınaqdır');

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: `<div style="font-family: var(--font-display, sans-serif); font-weight: 800; font-size: 19px; color: #DC3545; display: flex; align-items: center; justify-content: center; gap: 8px;">
                  <span style="display: inline-block; animation: pulse-dot 1s infinite;">🚨</span>
                  <span>${sosTitle}</span>
                </div>`,
        html: `
          <div style="text-align: left; background: rgba(220, 53, 69, 0.08); border: 1px solid rgba(220, 53, 69, 0.35); border-radius: 14px; padding: 14px; margin: 14px 0 6px 0; color: #E5E7EB; font-size: 12.5px; line-height: 1.6;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; color: #FFF; font-weight: 700;">
              <span>📍</span>
              <span>${gpsText}: <span style="color: #69E5FF; font-family: monospace;">40.4093° N, 49.8671° E</span> (Bakı)</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
              <span>📡</span>
              <span>${familyText}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px; color: #FF8A98; font-weight: 600;">
              <span>📞</span>
              <span>${serviceText}</span>
            </div>
          </div>
        `,
        background: '#121218',
        color: '#FFFFFF',
        showCancelButton: true,
        confirmButtonText: callBtnText,
        cancelButtonText: closeBtnText,
        confirmButtonColor: '#DC3545',
        cancelButtonColor: '#2B2B38',
        reverseButtons: true,
        customClass: {
          popup: 'airsen-swal-popup',
          confirmButton: 'airsen-swal-confirm-btn',
          cancelButton: 'airsen-swal-cancel-btn'
        }
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'tel:112';
        }
      });
    } else {
      window.location.href = 'tel:112';
    }
  });
}

function animateValue(element, start, end, duration) {
  let startTimestamp = null;
  const step = (timestamp) => {
    if (!startTimestamp) startTimestamp = timestamp;
    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
    element.textContent = Math.floor(progress * (end - start) + start);
    if (progress < 1) {
      window.requestAnimationFrame(step);
    }
  };
  window.requestAnimationFrame(step);
}

/* Audio Synthesizer */
function getAudioContext() {
  if (!audioCtx) {
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    if (AudioContext) audioCtx = new AudioContext();
  }
  if (audioCtx && audioCtx.state === 'suspended') {
    audioCtx.resume();
  }
  return audioCtx;
}

function playWarningBeep() {
  try {
    const ctx = getAudioContext();
    if (!ctx) return;
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.type = 'sine';
    osc.frequency.setValueAtTime(880, ctx.currentTime); // A5
    gain.gain.setValueAtTime(0.08, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
    osc.connect(gain);
    gain.connect(ctx.destination);
    osc.start();
    osc.stop(ctx.currentTime + 0.3);
  } catch (e) {}
}

function startSiren() {
  stopSiren();
  try {
    const ctx = getAudioContext();
    if (!ctx) return;
    sirenOscillator = ctx.createOscillator();
    const gain = ctx.createGain();
    sirenOscillator.type = 'sawtooth';
    sirenOscillator.frequency.setValueAtTime(600, ctx.currentTime);
    gain.gain.setValueAtTime(0.05, ctx.currentTime);
    sirenOscillator.connect(gain);
    gain.connect(ctx.destination);
    sirenOscillator.start();

    let high = false;
    sirenInterval = setInterval(() => {
      if (!ctx || !sirenOscillator) return;
      sirenOscillator.frequency.setTargetAtTime(high ? 600 : 1000, ctx.currentTime, 0.1);
      high = !high;
    }, 250);
  } catch (e) {}
}

function stopSiren() {
  if (sirenInterval) {
    clearInterval(sirenInterval);
    sirenInterval = null;
  }
  if (sirenOscillator) {
    try {
      sirenOscillator.stop();
      sirenOscillator.disconnect();
    } catch (e) {}
    sirenOscillator = null;
  }
}

/* ==========================================================================
   Google Play CTA Moderation Handler
   ========================================================================== */
function initGooglePlayCTA() {
  const gpButtons = document.querySelectorAll('.js-google-play-btn');
  const modal = document.getElementById('gpModerationModal');
  const closeBtn = document.getElementById('gpModalClose');

  gpButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      trackEvent('google_play_click');
      const isModeration = btn.dataset.status === 'moderation';
      if (isModeration) {
        e.preventDefault();
        modal?.classList.add('is-open');
      }
    });
  });

  closeBtn?.addEventListener('click', () => {
    modal?.classList.remove('is-open');
  });

  modal?.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('is-open');
  });
}

/* ==========================================================================
   Review Modal & Rating Picker (Mandatory Premoderation)
   ========================================================================== */
function initReviewModal() {
  const openBtn = document.getElementById('btnOpenReviewModal');
  const modal = document.getElementById('reviewModal');
  const closeBtn = document.getElementById('reviewModalClose');
  const form = document.getElementById('reviewForm');
  const starPicker = document.getElementById('starPicker');
  const ratingInput = document.getElementById('reviewRatingInput');

  openBtn?.addEventListener('click', () => {
    modal?.classList.add('is-open');
    trackEvent('review_modal_opened');
  });

  closeBtn?.addEventListener('click', () => {
    modal?.classList.remove('is-open');
  });

  modal?.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('is-open');
  });

  // Interactive 5-star rating picker with half-star precision (0.5 to 5.0)
  const scorePill = document.getElementById('liveScorePill');
  const liveText = document.getElementById('liveRatingText');

  const getRatingLabel = (val) => {
    if (val >= 5.0) return '5.0 — Möhtəşəm';
    if (val >= 4.5) return '4.5 — Çox Yaxşı';
    if (val >= 4.0) return '4.0 — Yaxşı';
    if (val >= 3.5) return '3.5 — Normal / Yaxşı';
    if (val >= 3.0) return '3.0 — Kafi';
    if (val >= 2.5) return '2.5 — Zəif';
    if (val >= 2.0) return '2.0 — Qeyri-kafi';
    if (val >= 1.5) return '1.5 — Pis';
    if (val >= 1.0) return '1.0 — Çox Pis';
    return '0.5 — Narazıyam';
  };

  const updateStarVisuals = (ratingVal, isHover = false) => {
    if (!starPicker) return;
    const stars = starPicker.querySelectorAll('.star-pick');
    stars.forEach(s => {
      const idx = parseInt(s.dataset.index);
      s.classList.remove('active', 'active-half', 'hovered', 'hovered-half');

      if (ratingVal >= idx) {
        s.classList.add(isHover ? 'hovered' : 'active');
      } else if (ratingVal >= (idx - 0.5)) {
        s.classList.add(isHover ? 'hovered-half' : 'active-half');
      }
    });

    if (scorePill) scorePill.textContent = `${ratingVal.toFixed(1)} / 5.0`;
    if (liveText) liveText.textContent = getRatingLabel(ratingVal);
  };

  if (starPicker) {
    const stars = starPicker.querySelectorAll('.star-pick');

    stars.forEach(star => {
      const getValFromEvent = (e) => {
        const rect = star.getBoundingClientRect();
        const idx = parseInt(star.dataset.index);
        const isLeftHalf = (e.clientX - rect.left) < (rect.width / 2);
        return isLeftHalf ? (idx - 0.5) : idx;
      };

      star.addEventListener('mousemove', (e) => {
        const hoverVal = getValFromEvent(e);
        updateStarVisuals(hoverVal, true);
      });

      star.addEventListener('click', (e) => {
        const clickedVal = getValFromEvent(e);
        if (ratingInput) ratingInput.value = clickedVal;
        updateStarVisuals(clickedVal, false);
      });
    });

    starPicker.addEventListener('mouseleave', () => {
      const currentRating = parseFloat(ratingInput?.value || 5.0);
      updateStarVisuals(currentRating, false);
    });
  }

  // Form submission via AJAX
  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    const payload = {
      name: document.getElementById('reviewNameInput')?.value,
      rating: parseFloat(document.getElementById('reviewRatingInput')?.value || 5.0),
      comment: document.getElementById('reviewCommentInput')?.value,
      locale: document.documentElement.lang || 'az'
    };

    try {
      const response = await fetch('/api/reviews/submit', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
      });

      const data = await response.json();
      if (data.success) {
        modal?.classList.remove('is-open');
        form.reset();
        
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: 'Təşəkkür edirik!',
            text: data.message || 'Rəyiniz qeydə alındı və moderasiyadan sonra dərc olunacaq.',
            background: '#15151C',
            color: '#FFFFFF',
            confirmButtonColor: '#C042F0'
          });
        } else {
          showToast(data.message || '✓ Təşəkkür edirik! Rəyiniz qeydə alındı və moderasiyadan sonra dərc olunacaq.');
        }
      } else {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Xəta',
            text: data.message || 'Zəhmət olmasa bütün xanaları düzgün doldurun.',
            background: '#15151C',
            color: '#FFFFFF',
            confirmButtonColor: '#FF3B5B'
          });
        } else {
          alert(data.message || 'Xəta baş verdi.');
        }
      }
    } catch (err) {
      console.error(err);
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Şəbəkə Xətası',
          text: 'Sorğu göndərilərkən xəta baş verdi.',
          background: '#15151C',
          color: '#FFFFFF',
          confirmButtonColor: '#FF3B5B'
        });
      } else {
        alert('Şəbəkə xətası baş verdi.');
      }
    } finally {
      if (submitBtn) submitBtn.disabled = false;
    }
  });
}

/* ==========================================================================
   Language Switcher (Instant Client-side Toggle & Sync)
   ========================================================================== */
function initLanguageSwitcher() {
  const langButtons = document.querySelectorAll('.js-lang-btn');

  langButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      const targetLang = btn.dataset.lang;
      if (!targetLang) return;

      // Update button active states
      langButtons.forEach(b => b.classList.remove('active'));
      document.querySelectorAll(`.js-lang-btn[data-lang="${targetLang}"]`).forEach(b => b.classList.add('active'));

      // Apply translations instantly to DOM
      applyTranslations(targetLang);

      // Inform server to sync session
      fetch(`/${targetLang}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      }).catch(() => {});

      // Update URL without reload
      if (window.history.pushState) {
        window.history.pushState(null, '', `/${targetLang}`);
      }

      trackEvent('language_switched', { language: targetLang });
    });
  });
}

function applyTranslations(lang) {
  document.documentElement.lang = lang;
  const dict = window.AIRSEN_TRANSLATIONS?.[lang];
  if (!dict) return;

  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.getAttribute('data-i18n');
    if (dict[key]) {
      if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
        el.placeholder = dict[key];
      } else {
        el.innerHTML = dict[key];
      }
    }
  });
}

/* ==========================================================================
   Toast Notification Helper
   ========================================================================== */
function showToast(message) {
  if (typeof Swal !== 'undefined') {
    const Toast = Swal.mixin({
      toast: true,
      position: 'bottom-end',
      showConfirmButton: false,
      timer: 3500,
      timerProgressBar: true,
      background: '#181822',
      color: '#FFFFFF'
    });
    Toast.fire({
      icon: 'success',
      title: message
    });
    return;
  }

  let toast = document.getElementById('globalToast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'globalToast';
    toast.className = 'toast-notification';
    document.body.appendChild(toast);
  }

  toast.innerHTML = `<span style="font-size: 18px;">🛡️</span> <span>${message}</span>`;
  toast.classList.add('is-visible');

  setTimeout(() => {
    toast.classList.remove('is-visible');
  }, 4500);
}

/* ==========================================================================
   Review Display: Auto-Glide Carousel with Progress Bar & Dots
   ========================================================================== */
function initReviewVariants() {
  const track = document.getElementById('reviewsSliderTrack');
  const prevBtn = document.getElementById('btnSliderPrev');
  const nextBtn = document.getElementById('btnSliderNext');
  const counter = document.getElementById('sliderCounter');
  const viewport = document.getElementById('reviewsSliderViewport');
  const progressBar = document.getElementById('sliderProgressBar');
  const dotsBox = document.getElementById('sliderDotsBox');

  if (!track || !viewport) return;

  let currentIndex = 0;
  const cards = track.querySelectorAll('.slider-card');
  const totalCards = cards.length;
  const SLIDE_DURATION = 5000; // 5 seconds per slide
  let progressTime = 0;
  let timerInterval = null;
  let isHovered = false;

  const getVisibleCardsCount = () => {
    const w = window.innerWidth;
    if (w <= 640) return 1;
    if (w <= 1024) return 2;
    return 3;
  };

  const renderDots = (maxIndex) => {
    if (!dotsBox) return;
    dotsBox.innerHTML = '';
    const totalPages = maxIndex + 1;
    if (totalPages <= 1) return;

    for (let i = 0; i < totalPages; i++) {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = `slider-dot-pill ${i === currentIndex ? 'active' : ''}`;
      dot.setAttribute('aria-label', `Slayd ${i + 1}`);
      dot.addEventListener('click', () => {
        currentIndex = i;
        resetTimer();
        updateSlider();
      });
      dotsBox.appendChild(dot);
    }
  };

  const updateDots = () => {
    if (!dotsBox) return;
    const dots = dotsBox.querySelectorAll('.slider-dot-pill');
    dots.forEach((dot, idx) => {
      dot.classList.toggle('active', idx === currentIndex);
    });
  };

  const updateSlider = () => {
    const visibleCount = getVisibleCardsCount();
    const maxIndex = Math.max(0, totalCards - visibleCount);

    if (currentIndex > maxIndex) currentIndex = maxIndex;
    if (currentIndex < 0) currentIndex = 0;

    if (cards.length > 0) {
      const firstCard = cards[0];
      const cardWidth = firstCard.getBoundingClientRect().width;
      const gap = 24;
      const offset = currentIndex * (cardWidth + gap);
      track.style.transform = `translateX(-${offset}px)`;
    }

    if (counter) {
      counter.textContent = `${currentIndex + 1} / ${Math.max(1, maxIndex + 1)}`;
    }

    if (prevBtn) prevBtn.disabled = currentIndex === 0;
    if (nextBtn) nextBtn.disabled = currentIndex >= maxIndex;

    updateDots();
  };

  const resetTimer = () => {
    progressTime = 0;
    if (progressBar) progressBar.style.width = '0%';
  };

  const startAutoSlideTimer = () => {
    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
      if (isHovered) return;

      const visibleCount = getVisibleCardsCount();
      const maxIndex = Math.max(0, totalCards - visibleCount);
      if (maxIndex <= 0) return;

      progressTime += 50;
      const progressPercent = Math.min(100, (progressTime / SLIDE_DURATION) * 100);

      if (progressBar) {
        progressBar.style.width = `${progressPercent}%`;
      }

      if (progressTime >= SLIDE_DURATION) {
        currentIndex = (currentIndex >= maxIndex) ? 0 : currentIndex + 1;
        resetTimer();
        updateSlider();
      }
    }, 50);
  };

  prevBtn?.addEventListener('click', () => {
    currentIndex--;
    resetTimer();
    updateSlider();
  });

  nextBtn?.addEventListener('click', () => {
    currentIndex++;
    resetTimer();
    updateSlider();
  });

  viewport.addEventListener('mouseenter', () => { isHovered = true; });
  viewport.addEventListener('mouseleave', () => { isHovered = false; });

  window.addEventListener('resize', () => {
    const visibleCount = getVisibleCardsCount();
    const maxIndex = Math.max(0, totalCards - visibleCount);
    renderDots(maxIndex);
    updateSlider();
  });

  // Touch swipe support
  let touchStartX = 0;
  let touchEndX = 0;

  viewport.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
  }, { passive: true });

  viewport.addEventListener('touchend', (e) => {
    touchEndX = e.changedTouches[0].screenX;
    const diff = touchStartX - touchEndX;
    const visibleCount = getVisibleCardsCount();
    const maxIndex = Math.max(0, totalCards - visibleCount);

    if (diff > 50 && currentIndex < maxIndex) {
      currentIndex++;
      resetTimer();
      updateSlider();
    } else if (diff < -50 && currentIndex > 0) {
      currentIndex--;
      resetTimer();
      updateSlider();
    }
  }, { passive: true });

  // Initialize
  setTimeout(() => {
    const visibleCount = getVisibleCardsCount();
    const maxIndex = Math.max(0, totalCards - visibleCount);
    renderDots(maxIndex);
    updateSlider();
    startAutoSlideTimer();
  }, 100);
}

/* ==========================================================================
   Scroll Reveal (Silky Smooth Staggered Fade-Up)
   ========================================================================== */
function initScrollReveal() {
  const targetElements = document.querySelectorAll('section, .how-bento-card, .feature-bento-card, .team-card, .investor-arch-card');
  if (!targetElements.length) return;

  targetElements.forEach((el, index) => {
    el.classList.add('reveal-on-scroll');
  });

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-revealed');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -40px 0px'
  });

  targetElements.forEach(el => observer.observe(el));
}

/* ==========================================================================
   Dynamic Sticky Header (Seamless Top Docking on Scroll)
   ========================================================================== */
function initStickyHeader() {
  const header = document.querySelector('.site-header');
  if (!header) return;

  const handleScroll = () => {
    if (window.scrollY > 20) {
      header.classList.add('is-scrolled');
    } else {
      header.classList.remove('is-scrolled');
    }
  };

  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll();
}

/* ==========================================================================
   AirSen "Airflow Stream" Back To Top Controller
   ========================================================================== */
function initBackToTop() {
  const bttBtn = document.getElementById('bttMainBtn') || document.querySelector('.airsen-back-to-top');
  if (!bttBtn) return;

  const handleScroll = () => {
    if (window.scrollY > 250) {
      bttBtn.classList.add('is-visible');
    } else {
      bttBtn.classList.remove('is-visible');
    }
  };

  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll();

  bttBtn.addEventListener('click', () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
}

/* ==========================================================================
   Mobile Navigation Drawer Controller
   ========================================================================== */
function initMobileNav() {
  const toggleBtn = document.getElementById('mobileMenuToggle');
  const overlay = document.getElementById('mobileNavOverlay');
  const closeBtn = document.getElementById('mobileDrawerClose');
  const drawerLinks = document.querySelectorAll('.mobile-nav-link');
  const logoLink = document.getElementById('mobileLogoLink');

  if (!toggleBtn || !overlay) return;

  const openDrawer = () => {
    overlay.classList.add('is-open');
    toggleBtn.classList.add('is-active');
    document.body.style.overflow = 'hidden';
  };

  const closeDrawer = () => {
    overlay.classList.remove('is-open');
    toggleBtn.classList.remove('is-active');
    document.body.style.overflow = '';
  };

  toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    if (overlay.classList.contains('is-open')) {
      closeDrawer();
    } else {
      openDrawer();
    }
  });

  closeBtn?.addEventListener('click', closeDrawer);

  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) closeDrawer();
  });

  drawerLinks.forEach(link => {
    link.addEventListener('click', closeDrawer);
  });

  logoLink?.addEventListener('click', closeDrawer);

  // Close on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && overlay.classList.contains('is-open')) {
      closeDrawer();
    }
  });
}

/* ==========================================================================
   Smooth Anchor Navigation & Active Scrollspy
   ========================================================================== */
function initScrollspy() {
  const sections = document.querySelectorAll('section[id]');
  const desktopLinks = document.querySelectorAll('.nav-links .nav-link');
  const mobileLinks = document.querySelectorAll('.mobile-drawer-links .mobile-nav-link');

  // Smooth scroll with precise header clearance on link click
  const allNavLinks = document.querySelectorAll('a[href^="#"]');
  allNavLinks.forEach(link => {
    link.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (!targetId || targetId === '#' || targetId === '#google-play') return;

      const targetEl = document.querySelector(targetId);
      if (targetEl) {
        e.preventDefault();
        const header = document.querySelector('.site-header');
        const headerHeight = header ? header.getBoundingClientRect().height : 75;
        const targetPos = targetEl.getBoundingClientRect().top + window.scrollY - (headerHeight + 20);

        window.scrollTo({
          top: Math.max(0, targetPos),
          behavior: 'smooth'
        });

        if (window.history.pushState) {
          window.history.pushState(null, '', targetId);
        }
      }
    });
  });

  // Active section scrollspy observer
  const handleScrollspy = () => {
    const scrollPos = window.scrollY + 160;

    sections.forEach(sec => {
      const top = sec.offsetTop;
      const height = sec.offsetHeight;
      const id = sec.getAttribute('id');

      if (scrollPos >= top && scrollPos < top + height) {
        desktopLinks.forEach(l => {
          l.classList.toggle('active', l.getAttribute('href') === `#${id}`);
        });
        mobileLinks.forEach(l => {
          l.classList.toggle('active', l.getAttribute('href') === `#${id}`);
        });
      }
    });
  };

  window.addEventListener('scroll', handleScrollspy, { passive: true });
  handleScrollspy();
}

