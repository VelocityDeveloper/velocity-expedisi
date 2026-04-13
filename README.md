# Velocity Expedisi

Plugin WordPress untuk manajemen ekspedisi, tarif, dan pelacakan resi (tracking) yang dikembangkan oleh Velocity Developer.

## Fitur Utama

- **Manajemen Tarif**: Kelola tarif pengiriman Nasional (antar kota) dan Internasional (antar negara).
- **Manajemen Resi**: Input data pengiriman lengkap termasuk informasi pengirim, penerima, dan detail barang.
- **Pelacakan Status (Tracking)**: Update status perjalanan paket secara real-time.
- **Frontend Shortcodes**: Tampilan pencarian tarif dan pelacakan resi yang modern dan responsif.
- **Data Lokasi Otomatis**: Integrasi data kota di Indonesia dan negara-negara di dunia untuk kemudahan input.

## Instalasi

1. Unggah folder `velocity-expedisi` ke direktori `/wp-content/plugins/`.
2. Aktifkan plugin melalui menu 'Plugins' di WordPress.
3. Plugin akan secara otomatis membuat tabel database yang diperlukan (`wp_tarif`, `wp_resi`, dan `wp_resi_tracking`).

## Penggunaan Shortcode

Plugin ini menyediakan dua shortcode utama untuk digunakan pada halaman atau postingan:

### 1. Cek Tarif (Ongkos Kirim)
Menampilkan form pencarian tarif berdasarkan asal, tujuan, dan berat barang.
```text
[cek_tarif]
```
*Parameter Opsional:*
- `type`: Menentukan tipe tarif yang ditampilkan secara default (`nasional` atau `internasional`). Contoh: `[cek_tarif type="internasional"]`.

### 2. Cek Resi (Pelacakan)
Menampilkan form pelacakan resi beserta timeline status perjalanannya.
```text
[cek_resi]
```

## Struktur Plugin

- `src/Admin/`: Logika menu admin dan tampilan manajemen data.
- `src/Core/`: Inti plugin, termasuk database handler dan data JSON kota/negara.
- `src/Frontend/`: Logika shortcode dan template tampilan untuk pengunjung.
- `assets/`: File CSS dan aset pendukung lainnya.

## Persyaratan Sistem

- WordPress 5.0+
- PHP 7.4+
- Bootstrap 5 (Otomatis dimuat oleh shortcode jika belum ada)

## Author

**Velocity Developer**
Website: [https://velocitydeveloper.com/](https://velocitydeveloper.com/)
