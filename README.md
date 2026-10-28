# Digital_Store (Dstore) - Marketplace Jual Beli Handphone (Laravel 10)

## Instalasi di Laragon
```bash
composer create-project laravel/laravel:^10.0 dstore     # (sudah dilakukan di folder ini)
# .env: DB_HOST=127.0.0.1, DB_DATABASE=dstore, DB_USERNAME=root, DB_PASSWORD=
mysql -uroot -e "CREATE DATABASE dstore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```
Buka aplikasi melalui Laragon di `http://localhost/Digital_Store/public`.
Pastikan Apache dan MySQL di Laragon berjalan. Jika menggunakan virtual host,
atur document root virtual host ke folder `public` proyek ini dan sesuaikan
`APP_URL` di `.env` dengan nama host tersebut.
Jika project di-clone: jalankan `composer install` dan `copy .env.example .env` terlebih dahulu.
Foto produk demo disimpan lokal di `public/images/products`, sehingga tetap tampil tanpa koneksi ke layanan gambar eksternal. Penjual dapat menggantinya melalui unggahan foto produk.

## Pembayaran QRIS
1. Customer checkout, lalu buka detail pesanan dan pilih **Bayar dengan QRIS**.
2. Scan QRIS yang ditampilkan menggunakan aplikasi mobile banking atau e-wallet, lalu masukkan nominal sesuai total pesanan.
3. Customer unggah bukti transfer melalui halaman pembayaran.
4. Penjual memeriksa bukti pada detail pesanan, lalu menyetujui atau menolak dengan alasan. Setelah ditolak, customer dapat mengunggah bukti baru.
5. Bukti transfer disimpan secara privat dan hanya dapat dilihat oleh penjual pemilik pesanan.

## Akun Dummy
| Role | Email | Password |
|---|---|---|
| Admin | admin@mail.com | password |
| Seller | seller@mail.com | password |
| Customer | customer@mail.com | password |

## Alur Bisnis
1. Seller mendaftar -> toko `is_verified=false` -> admin verifikasi di menu **Toko**.
2. Customer checkout -> order dipecah **per toko** -> bayar melalui QRIS dan mengunggah bukti -> seller menyetujui pembayaran -> input resi -> customer "Pesanan Diterima".
3. Saat selesai, saldo toko bertambah (total - komisi). Seller mengajukan penarikan; admin setujui/tolak (ditolak = saldo dikembalikan).
4. Customer dapat mengajukan sengketa saat status *dikirim*; admin menyetujui (order batal, stok kembali) atau menolak.

## Testing
Test memakai database `dstore_test` (`mysql -uroot -e "CREATE DATABASE dstore_test"`), lalu `php artisan test`.
