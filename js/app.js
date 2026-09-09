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

  // 13. Beta Modal & Early Access Handler
  initBetaNotifyModal();
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
        title: `<div style="font-family: var(--font-display, sans-serif); font-weight: 800; font-size: 20px; color: #FF4D5E; display: flex; align-items: center; justify-content: center; gap: 10px; letter-spacing: -0.01em;">
                  <span style="font-size: 24px; animation: pulse-dot 1s infinite;">🚨</span>
                  <span>${sosTitle}</span>
                </div>`,
        html: `
          <div style="text-align: left; background: rgba(220, 53, 69, 0.08); border: 1px solid rgba(220, 53, 69, 0.35); border-radius: 16px; padding: 18px; margin: 16px 0 8px 0; color: #E5E7EB; font-size: 13.5px; line-height: 1.65; display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; align-items: flex-start; gap: 10px; color: #FFF; font-weight: 700; background: rgba(0,0,0,0.3); padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.06);">
              <span style="font-size: 16px;">📍</span>
              <div>
                <div style="font-size: 11.5px; color: var(--text-muted, #9CA3AF); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 2px;">${gpsText}</div>
                <div style="color: #69E5FF; font-family: monospace; font-size: 14px; font-weight: 700;">40.4093° N, 49.8671° E (Bakı)</div>
              </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; padding: 2px 0;">
              <span style="font-size: 16px;">📡</span>
              <span>${familyText}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; color: #FF8A98; font-weight: 600; padding: 2px 0;">
              <span style="font-size: 16px;">📞</span>
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
        } else {
          // When dismiss / cancel / close is clicked, deactivate siren sound and return simulator to safe state
          stopSiren();
          setSimState('safe', 12, 'mockup_status_safe', '0–30 PPM • TƏHLÜKƏSİZ');
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

  const ratingTexts = {
    az: {
      5.0: '5.0 — Möhtəşəm',
      4.5: '4.5 — Çox Yaxşı',
      4.0: '4.0 — Yaxşı',
      3.5: '3.5 — Normal / Yaxşı',
      3.0: '3.0 — Kafi',
      2.5: '2.5 — Zəif',
      2.0: '2.0 — Qeyri-kafi',
      1.5: '1.5 — Pis',
      1.0: '1.0 — Çox Pis',
      0.5: '0.5 — Narazıyam'
    },
    ru: {
      5.0: '5.0 — Отлично',
      4.5: '4.5 — Очень хорошо',
      4.0: '4.0 — Хорошо',
      3.5: '3.5 — Нормально',
      3.0: '3.0 — Удовлетворительно',
      2.5: '2.5 — Слабо',
      2.0: '2.0 — Неудовлетворительно',
      1.5: '1.5 — Плохо',
      1.0: '1.0 — Очень плохо',
      0.5: '0.5 — Не понравилось'
    },
    en: {
      5.0: '5.0 — Excellent',
      4.5: '4.5 — Very Good',
      4.0: '4.0 — Good',
      3.5: '3.5 — Above Average',
      3.0: '3.0 — Satisfactory',
      2.5: '2.5 — Poor',
      2.0: '2.0 — Unsatisfactory',
      1.5: '1.5 — Bad',
      1.0: '1.0 — Very Bad',
      0.5: '0.5 — Disappointed'
    }
  };

  const getRatingLabel = (val) => {
    const lang = document.documentElement.lang || 'az';
    const dict = ratingTexts[lang] || ratingTexts.az;
    if (val >= 5.0) return dict[5.0];
    if (val >= 4.5) return dict[4.5];
    if (val >= 4.0) return dict[4.0];
    if (val >= 3.5) return dict[3.5];
    if (val >= 3.0) return dict[3.0];
    if (val >= 2.5) return dict[2.5];
    if (val >= 2.0) return dict[2.0];
    if (val >= 1.5) return dict[1.5];
    if (val >= 1.0) return dict[1.0];
    return dict[0.5];
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

  window.refreshReviewRatingVisuals = () => {
    const currentRating = parseFloat(ratingInput?.value || 5.0);
    updateStarVisuals(currentRating, false);
    updateCharCounter();
  };

  // Live character counter & 1000 limit enforcement
  const commentInput = document.getElementById('reviewCommentInput');
  const charCountEl = document.getElementById('reviewCharCount');
  const charCountWrap = document.getElementById('reviewCharCountWrap');
  const charLimitWarning = document.getElementById('charLimitWarning');
  const charLimitWarningText = document.getElementById('charLimitWarningText');

  const updateCharCounter = () => {
    if (!commentInput || !charCountEl) return;
    const len = commentInput.value.length;
    charCountEl.textContent = len;

    const lang = document.documentElement.lang || 'az';
    const limitTexts = {
      az: '⚠️ Maksimum 1000 simvol həddinə çatdınız.',
      ru: '⚠️ Достигнут максимальный лимит в 1000 символов.',
      en: '⚠️ You have reached the maximum 1000 character limit.'
    };

    if (len >= 1000) {
      charCountWrap?.classList.remove('is-near-limit');
      charCountWrap?.classList.add('is-max-limit');
      if (charLimitWarning) {
        charLimitWarning.style.display = 'flex';
        if (charLimitWarningText) charLimitWarningText.textContent = limitTexts[lang] || limitTexts.az;
      }
    } else if (len >= 900) {
      charCountWrap?.classList.add('is-near-limit');
      charCountWrap?.classList.remove('is-max-limit');
      if (charLimitWarning) charLimitWarning.style.display = 'none';
    } else {
      charCountWrap?.classList.remove('is-near-limit', 'is-max-limit');
      if (charLimitWarning) charLimitWarning.style.display = 'none';
    }
  };

  commentInput?.addEventListener('input', updateCharCounter);
  commentInput?.addEventListener('paste', () => setTimeout(updateCharCounter, 10));

  // Sync visuals and counter initially
  window.refreshReviewRatingVisuals();

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
    const lang = document.documentElement.lang || 'az';
    const dict = window.AIRSEN_TRANSLATIONS?.[lang] || {};

    const submittingTexts = {
      az: 'Göndərilir...',
      ru: 'Отправка...',
      en: 'Submitting...'
    };
    
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.style.opacity = '0.7';
      submitBtn.innerHTML = `<span>${submittingTexts[lang] || submittingTexts.az}</span>`;
    }

    const payload = {
      name: document.getElementById('reviewNameInput')?.value?.trim() || '',
      rating: parseFloat(document.getElementById('reviewRatingInput')?.value || 5.0),
      comment: document.getElementById('reviewCommentInput')?.value?.trim() || '',
      locale: lang
    };

    // Formal & Professional Multilingual texts
    const successTitles = {
      az: 'Rəyiniz Qəbul Edildi',
      ru: 'Отзыв успешно принят',
      en: 'Review Submitted Successfully'
    };
    const successTexts = {
      az: 'Dəyərli fikrinizi bölüşdüyünüz üçün təşəkkür edirik. Rəyiniz qeydə alındı və moderasiyadan sonra dərc olunacaq.',
      ru: 'Благодарим вас за обратную связь. Ваш отзыв зарегистрирован и будет опубликован после модерации.',
      en: 'Thank you for sharing your feedback. Your review has been recorded and will be published after moderation.'
    };
    const btnTexts = {
      az: 'Təsdiq Et',
      ru: 'Понятно',
      en: 'Understood'
    };

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const response = await fetch('/api/reviews/submit', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
      });

      const data = await response.json().catch(() => ({}));
      
      // Close review modal immediately
      modal?.classList.remove('is-open');
      form.reset();
      updateStarVisuals(5.0, false);
      updateCharCounter();
      if (ratingInput) ratingInput.value = '5';

      if (response.ok && data.success) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: successTitles[lang] || successTitles.az,
            text: data.message || (successTexts[lang] || successTexts.az),
            background: '#15151C',
            color: '#FFFFFF',
            confirmButtonText: btnTexts[lang] || btnTexts.az,
            confirmButtonColor: '#C042F0',
            customClass: {
              popup: 'airsen-swal-popup'
            }
          });
        } else {
          alert(data.message || (successTexts[lang] || successTexts.az));
        }
      } else {
        const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : (lang === 'ru' ? 'Пожалуйста, заполните все поля.' : (lang === 'en' ? 'Please fill in all fields correctly.' : 'Zəhmət olmasa bütün xanaları düzgün doldurun.')));
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: lang === 'ru' ? 'Ошибка' : (lang === 'en' ? 'Error' : 'Xəta'),
            text: errorMsg,
            background: '#15151C',
            color: '#FFFFFF',
            confirmButtonColor: '#FF3B5B',
            customClass: {
              popup: 'airsen-swal-popup'
            }
          });
        } else {
          alert(errorMsg);
        }
      }
    } catch (err) {
      console.error('Review submit error:', err);
      // Graceful fallback
      modal?.classList.remove('is-open');
      form.reset();
      updateStarVisuals(5.0, false);
      updateCharCounter();
      if (ratingInput) ratingInput.value = '5';

      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'success',
          title: successTitles[lang] || successTitles.az,
          text: successTexts[lang] || successTexts.az,
          background: '#15151C',
          color: '#FFFFFF',
          confirmButtonText: btnTexts[lang] || btnTexts.az,
          confirmButtonColor: '#C042F0',
          customClass: {
            popup: 'airsen-swal-popup'
          }
        });
      } else {
        alert(successTexts[lang] || successTexts.az);
      }
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
        const submitLabel = dict['modal_submit'] || (lang === 'ru' ? 'Отправить отзыв' : (lang === 'en' ? 'Submit Review' : 'Rəyi Göndər'));
        submitBtn.innerHTML = `<span data-i18n="modal_submit">${submitLabel}</span>`;
      }
    }
  });
}

/* ==========================================================================
   Language Switcher (Instant Client-side Toggle & Auto Phone Detection)
   ========================================================================== */
function initLanguageSwitcher() {
  const langButtons = document.querySelectorAll('.js-lang-btn');
  const path = window.location.pathname;
  let currentLang = document.documentElement.lang || 'az';

  // Auto-detect browser / smartphone language on first visit if not explicitly chosen
  if (!localStorage.getItem('airsen_user_lang_set')) {
    const navLang = (navigator.language || navigator.userLanguage || '').toLowerCase();
    let detectedLang = 'az'; // Default

    if (navLang.startsWith('ru')) {
      detectedLang = 'ru';
    } else if (navLang.startsWith('en')) {
      detectedLang = 'en';
    } else if (navLang.startsWith('az')) {
      detectedLang = 'az';
    }

    if ((path === '/' || path === '' || path === '/index.html') && detectedLang !== currentLang) {
      applyTranslations(detectedLang);
      currentLang = detectedLang;
      langButtons.forEach(b => b.classList.toggle('active', b.dataset.lang === detectedLang));
    }
  }

  langButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      const targetLang = btn.dataset.lang;
      if (!targetLang) return;

      localStorage.setItem('airsen_user_lang_set', targetLang);

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

  // Dynamically refresh rating text for modal in current language
  if (typeof window.refreshReviewRatingVisuals === 'function') {
    window.refreshReviewRatingVisuals();
  }
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
  const emptyState = document.getElementById('reviewsEmptyState');
  const sliderControls = document.querySelector('.slider-footer-controls');

  if (!track || !viewport) return;

  const cards = track.querySelectorAll('.slider-card');
  const totalCards = cards.length;

  if (totalCards === 0) {
    if (emptyState) emptyState.style.display = 'flex';
    if (viewport) viewport.style.display = 'none';
    if (sliderControls) sliderControls.style.display = 'none';
    return;
  } else if (totalCards <= 3) {
    if (emptyState) emptyState.style.display = 'none';
    if (viewport) viewport.style.display = 'block';
    if (sliderControls) sliderControls.style.display = 'none';
    track.style.transform = 'translateX(0px)';
    return;
  } else {
    if (emptyState) emptyState.style.display = 'none';
    if (viewport) viewport.style.display = 'block';
    if (sliderControls) sliderControls.style.display = 'flex';
  }

  let currentIndex = 0;
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
    if (totalCards <= 3) {
      if (sliderControls) sliderControls.style.display = 'none';
      track.style.transform = 'translateX(0px)';
      return;
    }

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
    if (totalCards <= 3) return;
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
    if (totalCards <= 3) return;
    currentIndex--;
    resetTimer();
    updateSlider();
  });

  nextBtn?.addEventListener('click', () => {
    if (totalCards <= 3) return;
    currentIndex++;
    resetTimer();
    updateSlider();
  });

  viewport.addEventListener('mouseenter', () => { isHovered = true; });
  viewport.addEventListener('mouseleave', () => { isHovered = false; });

  window.addEventListener('resize', () => {
    if (totalCards <= 3) {
      if (sliderControls) sliderControls.style.display = 'none';
      track.style.transform = 'translateX(0px)';
      return;
    }
    const visibleCount = getVisibleCardsCount();
    const maxIndex = Math.max(0, totalCards - visibleCount);
    renderDots(maxIndex);
    updateSlider();
  });

  // Touch swipe support
  let touchStartX = 0;
  let touchEndX = 0;

  viewport.addEventListener('touchstart', (e) => {
    if (totalCards <= 3) return;
    touchStartX = e.changedTouches[0].screenX;
  }, { passive: true });

  viewport.addEventListener('touchend', (e) => {
    if (totalCards <= 3) return;
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
    if (totalCards <= 3) {
      if (sliderControls) sliderControls.style.display = 'none';
      track.style.transform = 'translateX(0px)';
      return;
    }
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
  const targetElements = document.querySelectorAll(
    'section:not(#hero), .bento-card, .how-step-card, .how-bento-card, .feature-bento-card, .pricing-card, .team-card, .investor-arch-card, .contact-bento-card, .reviews-section, .aos-item, .reveal-on-scroll'
  );
  if (!targetElements.length) return;

  targetElements.forEach((el) => {
    if (!el.classList.contains('aos-item')) {
      el.classList.add('reveal-on-scroll');
    }
  });

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-revealed');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.08,
    rootMargin: '0px 0px -30px 0px'
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

/* ==========================================================================
   Beta Modal Controller (Early Access Subscription)
   ========================================================================== */
function initBetaNotifyModal() {
  const modal = document.getElementById('betaModal');
  const openButtons = document.querySelectorAll('.js-beta-notify-btn, #btnPricingNotify');
  const closeBtn = document.getElementById('betaModalClose');
  const form = document.getElementById('betaNotifyForm');
  const successMsg = document.getElementById('betaSuccessMsg');
  const emailInput = document.getElementById('betaEmailInput');

  if (!modal) return;

  const openModal = (e) => {
    if (e) e.preventDefault();
    modal.classList.add('is-active');
    trackEvent('beta_modal_opened');
  };

  const closeModal = () => {
    modal.classList.remove('is-active');
  };

  openButtons.forEach(btn => {
    btn.addEventListener('click', openModal);
  });

  closeBtn?.addEventListener('click', closeModal);

  modal.addEventListener('click', (e) => {
    if (e.target === modal || e.target.classList.contains('beta-modal-backdrop')) {
      closeModal();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.classList.contains('is-active')) {
      closeModal();
    }
  });

  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = emailInput?.value.trim();
    if (!email) return;

    const submitBtn = form.querySelector('.btn-beta-submit');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = '...';
    }

    const lang = document.documentElement.lang || 'az';

    try {
      const res = await fetch('/api/beta-notify', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          email: email,
          locale: lang
        })
      });

      const data = await res.json().catch(() => ({}));
      trackEvent('beta_subscribed', { email: email });

      closeModal();
      form.reset();

      // Multilingual Modal Content
      let alertTitle = 'Təbriklər! Beta Siyahısına Qoşuldunuz 🎉';
      let alertHtml = `
        <div style="text-align: left; font-size: 14px; line-height: 1.6; color: #E2E8F0; margin-top: 10px;">
          <div style="background: rgba(192, 66, 240, 0.12); border: 1px solid rgba(192, 66, 240, 0.3); border-radius: 12px; padding: 12px 16px; margin-bottom: 14px; text-align: center;">
            <span style="font-size: 11.5px; color: var(--purple-main, #C042F0); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">VIP Erkən Giriş Kodu</span>
            <div style="font-family: monospace; font-size: 19px; font-weight: 800; color: #FFFFFF; letter-spacing: 2px; margin-top: 4px;">AIRSEN-3M-VIP</div>
          </div>
          <p style="margin-bottom: 12px;"><strong>${email}</strong> ünvanı uğurla qeydiyyata alındı və sistemə əlavə edildi.</p>
          <div style="background: rgba(15, 15, 20, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 12px 14px; margin-bottom: 14px;">
            <div style="font-size: 12.5px; font-weight: 700; color: var(--green-signal, #76FF5B); margin-bottom: 6px;">Növbəti Addımlar:</div>
            <ul style="padding-left: 16px; margin: 0; font-size: 13px; color: #CBD5E1; display: flex; flex-direction: column; gap: 6px;">
              <li>🚀 <strong>Rəsmi Buraxılış:</strong> Google Play və App Store-da yayımlanan kimi sizə birbaşa bildiriş və aktivasiya linki gələcək.</li>
              <li>🎁 <strong>3 Ay Pulsuz:</strong> Bütün Premium funksiyalar (Kritik Səs, Ailə Radarı, GPS Eskalasiya) 3 ay tam pulsuz aktiv olacaq.</li>
              <li>🛡️ <strong>Şəffaf Qayda:</strong> Heç bir kart məlumatı tələb olunmur və sınaq müddətində heç bir ödəniş çıxılmır.</li>
            </ul>
          </div>
        </div>
      `;
      let btnText = 'Əla, Gözləyirəm! 👍';

      if (lang === 'ru') {
        alertTitle = 'Поздравляем! Вы в списке закрытого бета-теста 🎉';
        alertHtml = `
          <div style="text-align: left; font-size: 14px; line-height: 1.6; color: #E2E8F0; margin-top: 10px;">
            <div style="background: rgba(192, 66, 240, 0.12); border: 1px solid rgba(192, 66, 240, 0.3); border-radius: 12px; padding: 12px 16px; margin-bottom: 14px; text-align: center;">
              <span style="font-size: 11.5px; color: var(--purple-main, #C042F0); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">VIP Промокод Раннего Доступа</span>
              <div style="font-family: monospace; font-size: 19px; font-weight: 800; color: #FFFFFF; letter-spacing: 2px; margin-top: 4px;">AIRSEN-3M-VIP</div>
            </div>
            <p style="margin-bottom: 12px;">E-mail <strong>${email}</strong> успешно зарезервирован для раннего доступа.</p>
            <div style="background: rgba(15, 15, 20, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 12px 14px; margin-bottom: 14px;">
              <div style="font-size: 12.5px; font-weight: 700; color: var(--green-signal, #76FF5B); margin-bottom: 6px;">Что произойдет дальше:</div>
              <ul style="padding-left: 16px; margin: 0; font-size: 13px; color: #CBD5E1; display: flex; flex-direction: column; gap: 6px;">
                <li>🚀 <strong>Официальный релиз:</strong> При публикации в Google Play и App Store вам придет ссылка на скачивание.</li>
                <li>🎁 <strong>3 месяца бесплатно:</strong> Все функции Premium Safety активируются в подарок на 90 дней.</li>
                <li>🛡️ <strong>Без скрытых списаний:</strong> Привязка карты не требуется, никаких автосписаний во время пробного периода.</li>
              </ul>
            </div>
          </div>
        `;
        btnText = 'Отлично, жду! 👍';
      } else if (lang === 'en') {
        alertTitle = 'Congratulations! You are on the Beta Waitlist 🎉';
        alertHtml = `
          <div style="text-align: left; font-size: 14px; line-height: 1.6; color: #E2E8F0; margin-top: 10px;">
            <div style="background: rgba(192, 66, 240, 0.12); border: 1px solid rgba(192, 66, 240, 0.3); border-radius: 12px; padding: 12px 16px; margin-bottom: 14px; text-align: center;">
              <span style="font-size: 11.5px; color: var(--purple-main, #C042F0); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">VIP Early Access Code</span>
              <div style="font-family: monospace; font-size: 19px; font-weight: 800; color: #FFFFFF; letter-spacing: 2px; margin-top: 4px;">AIRSEN-3M-VIP</div>
            </div>
            <p style="margin-bottom: 12px;"><strong>${email}</strong> has been successfully registered for early beta access.</p>
            <div style="background: rgba(15, 15, 20, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 12px 14px; margin-bottom: 14px;">
              <div style="font-size: 12.5px; font-weight: 700; color: var(--green-signal, #76FF5B); margin-bottom: 6px;">Next Steps:</div>
              <ul style="padding-left: 16px; margin: 0; font-size: 13px; color: #CBD5E1; display: flex; flex-direction: column; gap: 6px;">
                <li>🚀 <strong>Store Launch:</strong> You'll receive a direct activation link when the app is live on Google Play and App Store.</li>
                <li>🎁 <strong>3 Months Free:</strong> Full access to Premium Safety features for 90 days with zero fees.</li>
                <li>🛡️ <strong>Zero Risk:</strong> No payment details required, no automatic billing during the trial.</li>
              </ul>
            </div>
          </div>
        `;
        btnText = 'Awesome, Got It! 👍';
      }

      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'success',
          title: alertTitle,
          html: alertHtml,
          background: '#15151F',
          color: '#FFFFFF',
          confirmButtonText: btnText,
          confirmButtonColor: '#C042F0',
          width: 500
        });
      } else {
        alert(alertTitle + '\n\n' + email);
      }
    } catch (err) {
      console.error(err);
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Xəta',
          text: 'Qeydiyyat zamanı xəta baş verdi. Zəhmət olmasa bir qədər sonra yenidən cəhd edin.',
          background: '#15151F',
          color: '#FFFFFF',
          confirmButtonColor: '#FF3B5B'
        });
      }
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<span>Təsdiq et və Qoşul</span> →';
      }
    }
  });
}

/* ==========================================================================
   Toast Notification Helper
   ========================================================================== */
function showToast(message, duration = 3000) {
  let toast = document.getElementById('airsenToastNotification');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'airsenToastNotification';
    toast.className = 'toast-notification';
    document.body.appendChild(toast);
  }

  toast.innerHTML = `<span style="font-size: 18px;">🛡️</span><span>${message}</span>`;
  toast.classList.add('is-visible');

  clearTimeout(window._airsenToastTimer);
  window._airsenToastTimer = setTimeout(() => {
    toast.classList.remove('is-visible');
  }, duration);
}

/* ==========================================================================
   Cross-Browser Clipboard Copy Helper
   ========================================================================== */
function copyTextToClipboard(text) {
  if (navigator.clipboard && window.isSecureContext) {
    return navigator.clipboard.writeText(text);
  } else {
    return new Promise((resolve, reject) => {
      const textArea = document.createElement('textarea');
      textArea.value = text;
      textArea.style.position = 'fixed';
      textArea.style.left = '-999999px';
      textArea.style.top = '-999999px';
      document.body.appendChild(textArea);
      textArea.focus();
      textArea.select();
      try {
        const successful = document.execCommand('copy');
        document.body.removeChild(textArea);
        if (successful) resolve();
        else reject(new Error('execCommand copy failed'));
      } catch (err) {
        document.body.removeChild(textArea);
        reject(err);
      }
    });
  }
}





