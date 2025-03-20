# svet.buket-online-store

# Установка Wordpress на сервер 

Для установки WordPress на сервер с Ubuntu, следуй этим шагам:

---

### **Шаг 1: Обновление системы**
Обнови пакеты на сервере:
```bash
sudo apt update && sudo apt upgrade -y
```

---

### **Шаг 2: Установка необходимых компонентов**
Установи Apache, MySQL и PHP:
```bash
sudo apt install apache2 mysql-server php php-mysql libapache2-mod-php php-cli php-curl php-gd php-xml php-mbstring unzip -y
```

---

### **Шаг 3: Настройка MySQL**
1. Запусти MySQL и настрой безопасный режим:
   ```bash
   sudo mysql_secure_installation
   ```
   Следуй инструкциям (установи пароль root и отключи анонимные входы).

2. Войди в MySQL:
   ```bash
   sudo mysql
   ```

3. Создай базу данных и пользователя для WordPress:
   ```sql
   CREATE DATABASE wordpress DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'wordpressuser'@'localhost' IDENTIFIED BY 'your_password';
   GRANT ALL PRIVILEGES ON wordpress.* TO 'wordpressuser'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

Замените `your_password` на надёжный пароль.

---

### **Шаг 4: Скачивание WordPress**
1. Перейди в папку `/var/www/`:
   ```bash
   cd /var/www/
   ```

2. Скачай последнюю версию WordPress:
   ```bash
   wget https://wordpress.org/latest.zip
   ```

3. Распакуй архив:
   ```bash
   unzip latest.zip
   mv wordpress /var/www/html/
   ```

4. Установи права доступа:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/wordpress
   sudo chmod -R 755 /var/www/html/wordpress
   ```

---

### **Шаг 5: Настройка Apache**
1. Создай конфигурационный файл для WordPress:
   ```bash
   sudo nano /etc/apache2/sites-available/wordpress.conf
   ```

2. Добавь в файл следующее содержимое:
   ```
   <VirtualHost *:80>
       ServerAdmin admin@example.com
       DocumentRoot /var/www/html/wordpress
       ServerName your_domain
       ServerAlias www.your_domain

       <Directory /var/www/html/wordpress>
           AllowOverride All
       </Directory>

       ErrorLog ${APACHE_LOG_DIR}/error.log
       CustomLog ${APACHE_LOG_DIR}/access.log combined
   </VirtualHost>
   ```

   Замени `your_domain` на домен или IP-адрес сервера.

3. Активируй сайт и модуль переписывания URL:
   ```bash
   sudo a2ensite wordpress
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

---

### **Шаг 6: Настройка WordPress**
1. Перейди в папку WordPress:
   ```bash
   cd /var/www/html/wordpress
   cp wp-config-sample.php wp-config.php
   ```

2. Отредактируй файл `wp-config.php`:
   ```bash
   sudo nano wp-config.php
   ```
   Найди строки и обнови их:
   ```php
   define('DB_NAME', 'wordpress');
   define('DB_USER', 'wordpressuser');
   define('DB_PASSWORD', 'your_password');
   define('DB_HOST', 'localhost');
   define('DB_CHARSET', 'utf8');
   define('DB_COLLATE', '');
   ```

---

### **Шаг 7: Завершение установки**
1. Перейди в браузер и открой:
   ```
   http://your_domain
   ```
2. Следуй инструкциям для завершения установки WordPress.

# Исправление файлов для полной работы .

Нужно явно задать `WP_HOME` и `WP_SITEURL`.  

---

### **Решение:**
Добавьте в `wp-config.php` перед строкой `/* That's all, stop editing! Happy publishing. */`:

```php
define('WP_HOME', 'ваш домен');
define('WP_SITEURL','ваш домен');

// Отключаем проверку SSL для админки (если возникают ошибки)
define('FORCE_SSL_ADMIN', false);

// Исправляем проблемы с редиректами
define('COOKIE_DOMAIN', '');
define('ADMIN_COOKIE_PATH', '/');
define('COOKIEPATH', '/');
define('SITECOOKIEPATH', '/');
```

---

### **Дополнительные шаги:**
1. **Очистите кэш браузера и cookies.**  
   - Либо откройте сайт в **режиме инкогнито** (`Ctrl+Shift+N`).

2. **Проверьте базу данных.**  
   Если WordPress продолжает редиректить, нужно вручную изменить `siteurl` и `home` в БД:  

   ```sh
   mysql -u root -p
   ```
   Затем выполните:
   ```sql
   USE wordpress;
   UPDATE wp_options SET option_value = 'ваш домен' WHERE option_name IN ('siteurl', 'home');
   ```
   (Замените `wordpress` на имя вашей БД).

3. **Перезапустите Apache:**
   ```sh
   sudo systemctl restart apache2
   ```
