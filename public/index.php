<?php

/**
 * Employee Management
 * 
 * Teknologi yang digunakan: 
 * 
 *  - Framework:
 * 
 *      - Bootstrap
 * 
 *  - Package PHP:
 * 
 *      - vlucas/phpdotenv
 * 
 * @author Rakhi Azfa Rifansya
 */

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Memuat konfigurasi yang ada di file .env
 * 
 */

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

/**
 * Mendefinisikan root directory dari aplikasi ini.
 * 
 */

define('ROOT_DIRECTORY', dirname(__DIR__));

/**
 * Memuat fungsi-fungsi penolong.
 * 
 */

require_once __DIR__ . '/../src/foundation/helpers.php';

/**
 * Memuat koneksi database.
 * 
 */

require_once __DIR__ . '/../src/database/connection.php';

/**
 * Membuat user dengan role admin.
 * 
 */

$user = [
    'name' => 'Admin',
    'email' => 'admin@example.co.id',
    'password' => password_hash('admin', PASSWORD_DEFAULT),
    'role' => 'admin',
];

/**
 * Cek apakah user dengan role admin sudah ada atau belum.
 * 
 */

$result = $connection->execute_query("SELECT * FROM users WHERE email = ?", [$user['email']]);
$admin = $result->fetch_assoc();

if (!$admin) {

    /**
     * Jika belum ada, maka buat user dengan role admin, jika sudah ada, maka lanjutkan ke proses selanjutnya.
     * 
     */

    $statement = $connection->execute_query("INSERT INTO users (name, email, password, role) VALUES (
        ?, ?, ?, ?
    )", [$user['name'], $user['email'], $user['password'], $user['role']]);
}

/**
 * Cek mode debug.
 * 
 */

if (!filter_var(env('APP_DEBUG'), FILTER_VALIDATE_BOOL)) {

    error_reporting(1);
}

/**
 * Mengubah default timezone.
 * 
 */

date_default_timezone_set(env('TIMEZONE', 'Asia/Jakarta'));

/**
 * Memulai sesi.
 * 
 */

session_start();

/**
 * Mengambil request url.
 * 
 */

$url = $_SERVER['PATH_INFO'] ?? '/dashboard';

/**
 * Daftar halaman yang membutuhkan akses login.
 * 
 */

$guardedPages = [
    '/dashboard',
];

/**
 * Memuat halaman sesuai request url yang diberikan.
 * 
 */

if ($url !== '/') {

    /**
     * Cek apakah halaman membutuhkan akses login.
     * 
     */

    if (in_array($url, $guardedPages)) {

        /**
         * Jika user belum login, maka lempar user ke halaman login.
         * 
         */

        if (!isset($_SESSION['user'])) {

            header('Location: ' . env('APP_URL') . '/login');
            die();
        }
    }

    /**
     * Cek ketersediaan file.
     * 
     */

    if (file_exists(ROOT_DIRECTORY . '/views' . $url . '.php')) {

        render($url . '.php');
    } else {

        render('404.php');
    }
}
