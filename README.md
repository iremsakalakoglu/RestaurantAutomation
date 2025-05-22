# Restaurant Automation System

## Proje Hakkında
Bu proje, restoran yönetimini otomatikleştirmek için geliştirilmiş kapsamlı bir web uygulamasıdır. Garson, yönetici ve mutfak personeli için ayrı paneller içerir. Sipariş yönetimi, masa takibi, stok kontrolü ve raporlama gibi temel restoran işlemlerini dijitalleştirir. Temel amacı müşterilerin kendi adisyonlarını web üzerinden kendileri oluşturarak süreci daha kolay bir şekilde yönetmektir.

## Özellikler
- 🍽️ Çoklu kullanıcı rolü (Garson, Yönetici, Mutfak)
- 📱 Responsive tasarım
- 📊 Gerçek zamanlı sipariş takibi
- 💳 Masa yönetimi
- 📦 Stok takibi
- 📈 Detaylı raporlama
- 🔔 Bildirim sistemi

## Teknik Detaylar
- **Framework:** Laravel
- **Frontend:** Tailwind CSS, JavaScript
- **Veritabanı:** MySQL
- **Ek Kütüphaneler:**
  - SweetAlert2 (Bildirimler için)
  - Font Awesome (İkonlar için)

## Kurulum

### Gereksinimler
- PHP >= 8.0
- Composer
- MySQL
- Node.js & NPM (Frontend assets için)

### Adımlar

1. **Projeyi İndirme**
   ```bash
   # Projeyi zip olarak indirdikten sonra
   unzip restaurant-automation.zip
   cd restaurant-automation
   ```

2. **Bağımlılıkları Yükleme**
   ```bash
   composer install
   npm install
   ```

3. **Veritabanı Kurulumu**
   ```bash
   # MySQL'e bağlanın
   mysql -u root -p
   
   # Veritabanı oluşturun
   CREATE DATABASE restaurant_automation;
   
   # Veritabanından çıkın
   exit
   
   # .sql dosyasını import edin
   mysql -u root -p restaurant_automation < database/restaurant_automation.sql
   ```

4. **Ortam Değişkenlerini Ayarlama**
   ```bash
   # .env dosyasını oluşturun
   cp .env.example .env
   
   # Veritabanı bağlantı bilgilerini düzenleyin
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=restaurant_automation
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

5. **Uygulama Anahtarını Oluşturma**
   ```bash
   php artisan key:generate
   ```

6. **Storage Linkini Oluşturma**
   ```bash
   php artisan storage:link
   ```

7. **Uygulamayı Başlatma**
   ```bash
   php artisan serve
   ```

## Kullanıcı Rolleri ve Erişim

### Garson Paneli
- Masa yönetimi
- Sipariş oluşturma ve takip
- Müşteri bilgilerini görüntüleme
- Sipariş durumu güncelleme

### Yönetici Paneli
- Tüm sistem ayarları
- Personel yönetimi
- Menü yönetimi
- Raporlama ve analiz
- Stok takibi

### Mutfak Paneli
- Sipariş görüntüleme
- Sipariş durumu güncelleme
- Stok durumu kontrolü

## Veritabanı Yapısı

### Ana Tablolar
- users (Kullanıcılar)
- tables (Masalar)
- orders (Siparişler)
- order_details (Sipariş Detayları)
- products (Ürünler)
- categories (Kategoriler)
- stocks (Stoklar)

## Güvenlik
- CSRF koruması
- XSS koruması
- SQL injection koruması
- Rol tabanlı yetkilendirme

## Hata Ayıklama
- Laravel log dosyaları: `storage/logs/laravel.log`
- PHP hata logları: PHP yapılandırmanıza bağlı olarak değişir

## Bakım ve Güncelleme
1. Composer paketlerini güncelleme:
   ```bash
   composer update
   ```

2. NPM paketlerini güncelleme:
   ```bash
   npm update
   ```

3. Veritabanı migrasyonlarını çalıştırma:
   ```bash
   php artisan migrate
   ```
   

## Sürüm Geçmişi
- v1.0.0 - İlk sürüm
  - Temel restoran yönetim özellikleri
  - Çoklu kullanıcı rolü desteği
  - Responsive tasarım