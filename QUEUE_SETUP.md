## ⚙️ SETUP QUEUE WORKER UNTUK NOTIFIKASI WhatsApp

Masalah Asli:

- Notifikasi WhatsApp dikirim secara **synchronous** (langsung/blocking)
- Dashboard langsung menampilkan "sent_at" saat itu juga
- Tetapi HP lambat terima karena API Fonnte memproses dengan delay

Solusi Implementasi:

1. **Queue Job** - `SendWhatsAppNotification.php` sudah dibuat
2. **Auto-Retry** - Retry 3x dengan backoff (10s, 30s, 60s)
3. **Rate Limiting** - Mencegah spam API Fonnte
4. **Database Tracking** - Notifikasi tercatat dengan status pending/sent

### 📋 LANGKAH SETUP

#### 1. Jalankan Migrasi (jika belum ada tabel jobs)

```bash
php artisan migrate
```

#### 2. Edit .env dan pastikan konfigurasi queue

```env
QUEUE_CONNECTION=database
DB_QUEUE_CONNECTION=mysql
DB_QUEUE_TABLE=jobs
DB_QUEUE_RETRY_AFTER=90
```

#### 3. Buat Rate Limiter untuk WhatsApp di config/rate-limit.php (jika belum ada)

```php
'whatsapp' => [
    'limit' => 10,
    'window' => 60, // per menit
],
```

#### 4. Publish Konfigurasi Queue (opsional)

```bash
php artisan vendor:publish --provider="Illuminate\Queue\QueueServiceProvider"
```

#### 5. Jalankan Queue Worker (Development)

```bash
php artisan queue:work database --queue=default --sleep=3
```

**Option A: Background Service (Development)**

```bash
start php artisan queue:work database --queue=default --sleep=3
```

**Option B: Menggunakan Supervisor (Production)**
Edit `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/app/artisan queue:work database --queue=default --sleep=3
autostart=true
autorestart=true
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/laravel-worker.log
```

Lalu:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

#### 6. Pantau Queue (Optional)

```bash
# Lihat jobs dalam queue
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry {id}

# Flush semua jobs
php artisan queue:flush
```

### 📊 ALUR NOTIFIKASI (Setelah Setup)

1. **Monitoring Check** (setiap 30 detik)
    - Detector status perangkat
    - Buat log notifikasi dengan status `queued`

2. **Queue Job** (delayed 2 detik)
    - WhatsApp job masuk antrian
    - Job worker memproses pengiriman
    - Jika gagal, auto-retry 3x

3. **WhatsApp Service**
    - Kirim via Fonnte API
    - Update log dengan status `sent` & timestamp aktual

4. **Dashboard Real-Time**
    - Polling `getRecentNotifications()` untuk update status
    - Menampilkan notifikasi dengan sent_at yang akurat

### ✅ BENEFITS

✓ **Tidak memblokir response HTTP** - Dashboard response cepat
✓ **Auto-retry jika gagal** - Lebih reliable pengiriman  
✓ **Rate limiting** - Tidak spam API Fonnte
✓ **Accurate tracking** - `sent_at` tercatat saat benar-benar terkirim
✓ **Scalable** - Bisa handle banyak notifikasi sekaligus

### 🔍 DEBUGGING

```bash
# Lihat logs queue worker
tail -f storage/logs/laravel.log | grep "WhatsApp"

# Lihat jobs dalam database
SELECT * FROM jobs;

# Lihat failed jobs
SELECT * FROM failed_jobs;
```

### ⚡ Testing Manual

```bash
# Test job tanpa queue (sync mode untuk testing)
QUEUE_CONNECTION=sync php artisan tinker

# Lalu run:
use App\Jobs\SendWhatsAppNotification;
SendWhatsAppNotification::dispatch(1, 'down', 'down', [])->now();
```
