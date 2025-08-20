# Simanja

**Simanja** adalah singkatan dari **Sistem Manajemen Kinerja**.  
Aplikasi Simanja ini adalah **replika dari WOLA (Workload Application) milik BPS Kabupaten Klaten**.  

Sistem ini dirancang untuk membantu **BPS Kota Semarang** dalam mengelola dan mengoptimalkan kinerja karyawan atau anggota tim.  
Simanja berfungsi sebagai alat untuk mengukur, mengelola, dan meningkatkan kinerja individu serta tim di dalam organisasi secara efektif dan efisien.

---

## Cara Instalasi
1. Clone repository ini
2. Jalankan `composer install`
3. Konfigurasi `.env` sesuai database dan environment kamu
4. Jalankan migrasi database dengan `php artisan migrate`
5. Jalankan server dengan `php artisan serve`
6. Jalankan untuk bisa melihat gambar upload bukti user`php artisan storage:link` 
