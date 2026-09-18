<?php

namespace App\Controllers;

use App\Models\User;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;

class SsoController extends BaseController
{
    // Controller logic here
    /**
     * GET /sso/callback?token=xxx
     * Terima token dari Joho, verify, login/CREATE user lokal PDCA
     */
    public function callback(Request $request)
    {
        $token = $request->token;

        if (!$token) {
            return $this->redirect('/login?error=missing_token');
        }

        // ── Verify signature ──
        $payload = $this->verify($token);
        if (!$payload) {
            return $this->redirect('/login?error=invalid_token');
        }

        // ── Cek expired ──
        if (($payload['exp'] ?? 0) < time()) {
            return $this->redirect('/login?error=token_expired');
        }

        // ══════════════════════════════════════════════════════
        // JIT PROVISIONING: cari atau create user di DB lokal PDCA
        // ══════════════════════════════════════════════════════

        $user = $this->findOrCreateUser($payload);

        if (!$user) {
            return $this->redirect('/login?error=user_creation_failed');
        }

        // ── Set session PDCA (framework native) ──
        // Sesuaikan dengan cara Session Anda bekerja
        $_SESSION['user'] = [
            'id'       => $user->id,
            'username' => $user->username,
            'name'     => $user->name,
            'role'     => $user->role,
            'email'    => $user->email,
        ];

        // Kalau framework Anda pakai Session::set(), pakai ini:
        // \Bpjs\Framework\Helpers\Session::set('user', (object) $_SESSION['user']);

        // ── Redirect ke board ──
        return $this->redirect('/');
    }

    /**
     * Cari user di DB lokal PDCA, atau CREATE kalau belum ada
     * 
     * Urutan lookup:
     *   1. external_source + external_id (link ke user Joho)
     *   2. username (kalau user pernah ada dengan username sama)
     *   3. email (kalau ada)
     */
    private function findOrCreateUser(array $payload): ?object
    {
        $source = 'joho';
        $extId  = (string) $payload['external_id'];
        $username = $payload['username'] ?? '';
        $email    = $payload['email'] ?? '';

        // ── 1. Cari by external link (paling akurat) ──
        $user = User::query()
            ->where('external_source', '=', $source)
            ->where('external_id', '=', $extId)
            ->first();

        if ($user) {
            $this->updateSyncFields($user, $payload);
            return $user;
        }

        // ── 2. Cari by username (mungkin sudah pernah di-create manual) ──
        if ($username) {
            $user = User::query()->where('username', '=', $username)->first();
            if ($user) {
                // Link-kan ke user Joho & update
                $this->linkAndUpdate($user, $payload);
                return $user;
            }
        }

        // ── 3. Cari by email ──
        if ($email) {
            $user = User::query()->where('email', '=', $email)->first();
            if ($user) {
                $this->linkAndUpdate($user, $payload);
                return $user;
            }
        }

        // ══════════════════════════════════════════════════════
        // 4. AUTO-CREATE user baru di DB lokal PDCA
        // ══════════════════════════════════════════════════════
        try {
            $newUser = User::create([
                'external_id'     => $extId,
                'external_source' => $source,
                'username'        => $username ?: ('joho_' . $extId),
                'name'            => $payload['name'] ?? $username,
                'email'           => $email ?: null,
                'role'            => $this->mapRole($payload['role'] ?? 'operator'),
                'password'        => null,   // SSO user, tidak punya password
                'synced_at'       => date('Y-m-d H:i:s'),
            ]);

            return $newUser;

        } catch (\Throwable $e) {
            error_log('[SSO] Failed to create user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update field sync saat user sudah ada
     */
    private function updateSyncFields(object $user, array $payload): void
    {
        $user->update([
            'name'      => $payload['name'] ?? $user->name,
            'email'     => $payload['email'] ?? $user->email,
            'role'      => $this->mapRole($payload['role'] ?? $user->role),
            'synced_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Link user existing ke Joho + update datanya
     */
    private function linkAndUpdate(object $user, array $payload): void
    {
        $user->update([
            'external_id'     => (string) $payload['external_id'],
            'external_source' => 'joho',
            'name'            => $payload['name'] ?? $user->name,
            'email'           => $payload['email'] ?? $user->email,
            'role'            => $this->mapRole($payload['role'] ?? $user->role),
            'synced_at'       => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Map role dari Joho → format PDCA
     */
    private function mapRole(string $raw): string
    {
        $key = str_replace([' ', '-', '_'], '', strtolower(trim($raw)));
        return [
            'operator'    => 'operator',
            'teamleader'  => 'team_leader',
            'groupleader' => 'group_leader',
            'manager'     => 'manager',
            'admin'       => 'admin',
        ][$key] ?? 'operator';
    }

    /**
     * Verify signature token dari Joho
     */
    private function verify(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2) return null;

        [$payloadB64, $sigB64] = $parts;
        $secret = env('SSO_SECRET');

        $expectedSig = hash_hmac('sha256', $payloadB64, $secret, true);
        $providedSig = base64_decode($sigB64);

        if (!hash_equals($expectedSig, $providedSig)) {
            return null;
        }

        $payload = json_decode(base64_decode($payloadB64), true);
        return is_array($payload) ? $payload : null;
    }

    /**
     * Simple redirect helper
     */
    // protected function redirect(string $url)
    // {
    //     header('Location: ' . $url, true, 302);
    //     exit;
    // }
}
