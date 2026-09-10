<?php
namespace Middlewares;

class SessionMiddleware {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            $config = config('session');
            if (!is_array($config)) {
                $config = [];
            }
            $lifetime = ($config['expire_on_close'] ?? false) ? 0 : ($config['lifetime'] ?? 120) * 60;
            ini_set('session.gc_maxlifetime', $lifetime);
            ini_set('session.cookie_lifetime', $lifetime);

            $sessionName = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '_', $config['app_name'] ?? 'bpjs')) . '_SESSID';
            session_name($sessionName);

            // Set save path untuk file session
            if (($config['driver'] ?? 'file') === 'file' && isset($config['storage_path'])) {
                if (!is_dir($config['storage_path'])) {
                    mkdir($config['storage_path'], 0755, true);
                }
                session_save_path($config['storage_path']);
            }

            ini_set('session.cookie_secure', ($config['secure'] ?? false) ? '1' : '0');
            ini_set('session.cookie_httponly', ($config['http_only'] ?? true) ? '1' : '0');
            ini_set('session.cookie_samesite', ucfirst($config['same_site'] ?? 'Lax'));

            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => '/',
                'domain' => '',
                'secure' => $config['secure'] ?? false,
                'httponly' => $config['http_only'] ?? true,
                'samesite' => ucfirst($config['same_site'] ?? 'Lax'),
            ]);

            session_start();

            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            self::storeDeviceFingerprint();
        }
    }

    public static function regenerate() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;
        }
    }

    public static function set($key, $value) {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $encrypt = config('session.encrypt', false);
            if ($encrypt) {
                $keyEnc = hash('sha256', env('APP_KEY', 'default_app_key'));
                $iv = random_bytes(16);
                $cipher = openssl_encrypt(serialize($value), 'AES-256-CBC', $keyEnc, 0, $iv);
                $_SESSION[$key] = base64_encode($iv . $cipher);
            } else {
                $_SESSION[$key] = $value;
            }

        }
    }

    public static function get($key) {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $encrypt = config('session.encrypt', false);
            $value = $_SESSION[$key] ?? null;
            if ($encrypt && $value !== null) {
                $keyEnc = hash('sha256', env('APP_KEY', 'default_app_key'));
                $data = base64_decode($value);
                $iv = substr($data, 0, 16);
                $cipher = substr($data, 16);
                $decrypted = openssl_decrypt($cipher, 'AES-256-CBC', $keyEnc, 0, $iv);
                return unserialize($decrypted);
            }
        }
        return null;
    }

    public static function delete($key) {
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION[$key])) {
            unset($_SESSION[$key]); 
        }
    }

    public static function destroy() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset(); 
            session_destroy(); 
        }
    }
    public static function validateDeviceFingerprint(): bool
    {
        $config = config('auth.device_fingerprint', []);

        if (!($config['enabled'] ?? false)) {
            return true;
        }

        $fingerprint = md5(
            ($_SERVER['HTTP_USER_AGENT'] ?? '') .
            ($_SERVER['REMOTE_ADDR'] ?? '')
        );

        if (!self::get('device_fingerprint')) {
            self::set('device_fingerprint', $fingerprint);
            return true;
        }

        $match = self::get('device_fingerprint') === $fingerprint;

        if (!($config['strict'] ?? false)) {
            return true;
        }

        return $match;
    }

    public static function storeDeviceFingerprint() {
        $fingerprint = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
        self::set('device_fingerprint', $fingerprint);
    }
}
