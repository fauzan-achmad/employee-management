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
 * Menghapus flash message yang sudah terpanggil.
 * 
 */

$flashMessages = $_SESSION['FLASH_MESSAGE'] ?? [];

foreach ($flashMessages as $key => $value) {

    if ($value['called']) {

        unset($flashMessages[$key]);
    }
}

$_SESSION['FLASH_MESSAGE'] = $flashMessages;

/**
 * Mengambil request url.
 * 
 */

$url = $_SERVER['REQUEST_URI'] !== '/' ? $_SERVER['REQUEST_URI'] : '/dashboard';

$position = strpos($url, '?') ?? false;

if ($position) {

    $url = substr($url, 0, $position);
}

/**
 * Daftar halaman yang membutuhkan akses login.
 * 
 */

$guardedPages = [
    '/dashboard',
    '/employees',
    '/employees/create',
    '/employees/detail',
    '/employees/edit',
    '/presences',
    '/user/presences',
    '/leave-of-absences',
];

/**
 * Daftar halaman yang tidak boleh menggunakan akses login.
 * 
 */

$unguardedPages = [
    '/login',
];

/**
 * Daftar halaman yang hanya boleh diakses oleh admin.
 * 
 */

$adminPages = [
    '/employees',
    '/employees/create',
    '/employees/detail',
    '/employees/edit',
    '/presences',
    '/shifts',
    '/shifts/create',
    '/shifts/edit',
    '/shifts/edit',
    '/leave-of-absences',
];

/**
 * Daftar halaman yang hanya boleh diakses oleh employee.
 * 
 */

$employeePages = [
    '/user/presences',
    '/user/leave-of-absences',
];

/**
 * Daftar aksi-aksi pada aplikasi ini.
 * 
 */

$actions = [

    '/actions/login' => function () {
        require_once __DIR__ . '/../actions/login.php';
        die();
    },

    '/actions/logout' => function () {
        require_once __DIR__ . '/../actions/logout.php';
        die();
    },

    '/actions/employees/store' => function () {
        require_once __DIR__ . '/../actions/employees/store.php';
        die();
    },

    '/actions/employees/update' => function () {
        require_once __DIR__ . '/../actions/employees/update.php';
        die();
    },

    '/actions/employees/delete' => function () {
        require_once __DIR__ . '/../actions/employees/delete.php';
        die();
    },

    '/actions/presences/store' => function () {
        require_once __DIR__ . '/../actions/presences/store.php';
        die();
    },

    '/actions/shifts/store' => function () {
        require_once __DIR__ . '/../actions/shifts/store.php';
        die();
    },

    '/actions/shifts/delete' => function () {
        require_once __DIR__ . '/../actions/shifts/delete.php';
        die();
    },

    '/actions/shifts/update' => function () {
        require_once __DIR__ . '/../actions/shifts/update.php';
        die();
    },

    '/actions/leave-of-absences/accept' => function () {
        require_once __DIR__ . '/../actions/leave-of-absences/accept.php';
        die();
    },

    '/actions/leave-of-absences/reject' => function () {
        require_once __DIR__ . '/../actions/leave-of-absences/reject.php';
        die();
    },
];

/**
 * Cek apakah user dengan role employee memiliki relasi dengan table employee.
 * Jika tidak memiliki relasi dengan table employee, maka delete user.
 * 
 */

if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'employee') {

    $userId = $_SESSION['user']['id'];

    $result = $connection->execute_query("SELECT * FROM employees WHERE user_id = ?", [$userId]);
    $employee = $result->fetch_assoc();

    if (!$employee) {

        $query = $connection->execute_query("DELETE FROM users WHERE id = ?", [$userId]);

        unset($_SESSION['user']);
    }
}

/**
 * Memuat halaman sesuai request url yang diberikan.
 * 
 */

if ($url !== '/') {

    /**
     * Cek apakah user mengakses sebuah aksi atau tidak.
     * 
     */

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($actions[$url])) {

        /**
         * Jika iya, maka jalankan aksi tersebut.
         * 
         */

        $actions[$url]();
    }

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
     * Cek apakah halaman tidak boleh menggunakan akses login.
     * 
     */

    if (in_array($url, $unguardedPages)) {

        /**
         * Jika tidak boleh menggunakan akses login, maka lempar user ke halaman dashboard.
         * 
         */

        if (isset($_SESSION['user'])) {

            header('Location: ' . env('APP_URL'));
            die();
        }
    }

    /**
     * Cek user role.
     * 
     */

    if (in_array($url, $adminPages)) {

        if (isset($_SESSION['user']) && $_SESSION['user']['role'] !== 'admin') {

            header('Location: ' . env('APP_URL') . '/403');
            die();
        }
    }

    if (in_array($url, $employeePages)) {

        if (isset($_SESSION['user']) && $_SESSION['user']['role'] !== 'employee') {

            header('Location: ' . env('APP_URL') . '/403');
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
