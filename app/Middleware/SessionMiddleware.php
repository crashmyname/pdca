<?php
namespace Middlewares;

class SessionMiddleware
{
    /** @var \Redis|null */
    private static $redis = null;

    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        $config = config('session');
        if (!is_array($config)) {
            $config = [];
        }

        // Lifetime cookie (detik)
        $lifetime = ($config['expire_on_close'] ?? false)
            ? 0
            : (int)($config['lifetime'] ?? 120) * 60;

        // Idle timeout (detik) — dipakai untuk GC server-side
        $idleTimeout = (int)($config['idle_timeout'] ?? 28800);
        $gcMax = max($lifetime, $idleTimeout);

        // Nama cookie session
        $sessionName = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '_', $config['app_name'] ?? 'bpjs')) . '_SESSID';
        session_name($sessionName);

        // Pilih handler
        $driver = $config['driver'] ?? 'file';

        if ($driver === 'redis') {
            self::configureRedisHandler($config, $gcMax);
        } else {
            self::configureFileHandler($config);
        }

        // Cookie & ini
        $secure   = (bool)($config['secure'] ?? false);
        $httpOnly = (bool)($config['http_only'] ?? true);
        $sameSite = ucfirst($config['same_site'] ?? 'Lax');

        ini_set('session.gc_maxlifetime', (string)$gcMax);
        ini_set('session.cookie_lifetime', (string)$lifetime);
        ini_set('session.cookie_secure', $secure ? '1' : '0');
        ini_set('session.cookie_httponly', $httpOnly ? '1' : '0');
        ini_set('session.cookie_samesite', $sameSite);
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite,
        ]);

        session_start();

        // CSRF token
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Idle timeout check (server-side)
        self::enforceIdleTimeout($idleTimeout);

        // Device fingerprint (hanya simpan kalau belum ada)
        self::storeDeviceFingerprint();
    }

    private static function enforceIdleTimeout(int $idleTimeout): void
    {
        if ($idleTimeout <= 0) return;
        if (!isset($_SESSION['_last_activity'])) {
            $_SESSION['_last_activity'] = time();
            return;
        }
        if (time() - (int)$_SESSION['_last_activity'] > $idleTimeout) {
            self::destroy();
            session_start();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $_SESSION['_last_activity'] = time();
    }

    private static function configureFileHandler(array $config): void
    {
        $path = $config['storage_path'] ?? (BPJS_BASE_PATH . '/storage/session');
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
        session_save_path($path);
    }

    private static function configureRedisHandler(array $config, int $gcMax): void
    {
        if (!extension_loaded('redis')) {
            throw new \RuntimeException('Ekstensi Redis belum terpasang. Jalankan: pecl install redis');
        }

        $redisCfg = $config['redis'] ?? [];

        $redis = new \Redis();
        $redis->connect(
            $redisCfg['host'] ?? '127.0.0.1',
            (int)($redisCfg['port'] ?? 6379),
            2.5
        );

        if (!empty($redisCfg['password']) && $redisCfg['password'] !== 'null') {
            $redis->auth($redisCfg['password']);
        }

        $db = (int)($redisCfg['database'] ?? 0);
        if ($db > 0) {
            $redis->select($db);
        }

        self::$redis = $redis;

        $prefix = $redisCfg['prefix'] ?? 'session:';

        // TTL = gc_maxlifetime (server-side)
        $handler = class_exists(\RedisSessionHandler::class)
            ? new \RedisSessionHandler($redis, $gcMax, $prefix)          // PHP >= 8.1
            : new RedisSessionHandler($redis, $gcMax, $prefix);          // custom

        session_set_save_handler($handler, true);
    }

    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['_last_activity'] = time();
        }
    }

    public static function set(string $key, $value): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) return;

        if (config('session.encrypt', false)) {
            $keyEnc = hash('sha256', env('APP_KEY', 'default_app_key'));
            $iv     = random_bytes(16);
            $cipher = openssl_encrypt(serialize($value), 'AES-256-CBC', $keyEnc, 0, $iv);
            if ($cipher === false) return;
            $_SESSION[$key] = base64_encode($iv . $cipher);
        } else {
            $_SESSION[$key] = $value;
        }
    }

    public static function get(string $key)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) return null;

        $value = $_SESSION[$key] ?? null;
        if ($value === null) return null;

        if (config('session.encrypt', false)) {
            $keyEnc = hash('sha256', env('APP_KEY', 'default_app_key'));
            $data = base64_decode($value, true);
            if ($data === false || strlen($data) < 17) return null;

            $iv     = substr($data, 0, 16);
            $cipher = substr($data, 16);

            $decrypted = openssl_decrypt($cipher, 'AES-256-CBC', $keyEnc, 0, $iv);
            if ($decrypted === false) return null;

            try { return unserialize($decrypted); }
            catch (\Throwable $e) { return null; }
        }

        return $value;
    }

    public static function has(string $key): bool
    {
        return session_status() === PHP_SESSION_ACTIVE && isset($_SESSION[$key]);
    }

    public static function delete(string $key): void
    {
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    // Alias agar cocok dengan Auth::logout()
    public static function remove(string $key): void
    {
        self::delete($key);
    }

    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $p = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $p['path'], $p['domain'], $p['secure'], $p['httponly']);
            }

            session_unset();
            session_destroy();
        }
    }

    public static function validateDeviceFingerprint(): bool
    {
        $config = config('auth.device_fingerprint', []);
        if (!($config['enabled'] ?? false)) return true;

        $fingerprint = md5(
            ($_SERVER['HTTP_USER_AGENT'] ?? '') .
            ($_SERVER['REMOTE_ADDR'] ?? '')
        );

        $stored = self::get('device_fingerprint');
        if ($stored === null) {
            self::set('device_fingerprint', $fingerprint);
            return true;
        }

        $match = hash_equals($stored, $fingerprint);

        if (!($config['strict'] ?? false)) {
            if (!$match) self::set('device_fingerprint', $fingerprint);
            return true;
        }

        return $match;
    }

    public static function storeDeviceFingerprint(): void
    {
        if (self::get('device_fingerprint') === null) {
            $fp = md5(
                ($_SERVER['HTTP_USER_AGENT'] ?? '') .
                ($_SERVER['REMOTE_ADDR'] ?? '')
            );
            self::set('device_fingerprint', $fp);
        }
    }

    public static function redis(): ?\Redis
    {
        return self::$redis;
    }
}