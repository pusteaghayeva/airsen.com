# AirSen

**AirSen** — Ağıllı hava keyfiyyəti monitorinqi və ekoloji məlumat platforması. İstifadəçilərə real vaxt rejimində hava keyfiyyəti indeksini izləmək, təmiz hava tövsiyələri almaq və mobil tətbiqlə inteqrasiya olunmuş imkanlardan yararlanmaq imkanı yaradır.

---

## 🌟 Əsas Xüsusiyyətlər

- **Müasir Landing Page:** Dinamik interfeys, interaktiv bölmələr və responsiv dizayn.
- **Çoxdilli Dəstək (Multi-language):** Azərbaycan (`az`), İngilis (`en`) və Rus (`ru`) dilləri arasında sürətli keçid.
- **Rəy və Qiymətləndirmə Sistemi:** İstifadəçilərin platforma haqqında rəy və reytinq bildirməsi, spamdan mühafizə və moderasiya.
- **İnzibatçı (Admin) Paneli:**
  - Təhlükəsiz autentifikasiya və sessiya idarəetməsi.
  - Ziyarətçi statistikası və analitika.
  - Rəylərin təsdiqlənməsi və idarə edilməsi.
  - Sayt tənzimləmələri və parametr konfiqurasiyaları.
- **Təhlükəsizlik və Məxfilik:**
  - CSP, X-Frame-Options və təhlükəsizlik başlıqları.
  - Anonimləşdirilmiş analitika (GDPR və məxfilik prinsiplərinə uyğun).
  - Rate limiting və CSRF qorunması.

---

## 🛠 Texnoloji Stek

- **Backend:** PHP 8.3+ / Laravel 12
- **Frontend:** Blade, Vanilla JavaScript, CSS3
- **Verilənlər Bazası:** PostgreSQL / SQLite
- **Alətlər:** Vite, Composer, PHPUnit

---

## 🚀 Quraşdırma və İşə Salma

### 1. Repositoriyanı klonlayın:
```bash
git clone https://github.com/pusteaghayeva/airsen.com.git
cd airsen.com
```

### 2. Asılılıqları yükləyin:
```bash
composer install
npm install
```

### 3. Konfiqurasiya faylını hazırlayın:
```bash
cp .env.example .env
php artisan key:generate
```

`.env` faylında verilənlər bazası (PostgreSQL və ya SQLite) parametrlərini təyin edin.

### 4. Miqrasiyaları və ilkin məlumatları icra edin:
```bash
php artisan migrate --seed
```

### 5. Serveri işə salın:
```bash
php artisan serve
```

Sayta daxil olmaq üçün: `http://127.0.0.1:8000`

---

## 🧪 Testlərin İcrası

Layihədəki vahid (unit) və funksional (feature) testləri yoxlamaq üçün:

```bash
php artisan test
```

---

## 📄 Lisenziya

Bu layihə [MIT lisenziyası](LICENSE) altında yayımlanır.
