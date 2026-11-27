# Tugas 2 — scan ulang

Gunakan salinan database pengujian. Pertahankan `.env` lokal dan APP_KEY yang sudah ada.
Pastikan APP_DEBUG=false, SESSION_HTTP_ONLY=true, SESSION_SAME_SITE=lax, SESSION_ENCRYPT=true, dan SESSION_SECURE_COOKIE=false (HTTP localhost saja; true untuk HTTPS produksi).

Dari folder projects:

```bash
php artisan optimize:clear
php artisan test
php -d expose_php=0 -S 127.0.0.1:8000 -t public server-zap.php
```

Hentikan `artisan serve` lama terlebih dahulu. Router lokal juga menerapkan header pada file statis. Untuk deployment gunakan konfigurasi server produksi sendiri, bukan router pengembangan ini.

Buka ZAP New Session, scan http://127.0.0.1:8000 dan lakukan pemindaian terautentikasi untuk role klien, developer, admin pada data dummy. Jangan menonaktifkan aturan scan. Periksa evidence setiap alert tersisa. Informational adalah observasi, bukan kerentanan. Jangan mengklaim nol risiko sebelum hasil aktual diperiksa.
