<?php
declare(strict_types=1);

/**
 * Contrôle d'accès par rôles (RBAC) — ISO 27002 5.15, 5.18, 8.2, 8.5
 *   admin    : tout, y compris gestion des utilisateurs et journal
 *   rssi     : écriture sur risques, actifs, mesures, exigences, audits
 *   auditeur : écriture sur audits et constats, lecture du reste
 *   lecteur  : lecture seule (Direction générale)
 */
const PERMISSIONS = [
    'admin'    => ['read', 'write', 'audit_write', 'admin'],
    'rssi'     => ['read', 'write', 'audit_write'],
    'auditeur' => ['read', 'audit_write'],
    'lecteur'  => ['read'],
];

function current_user(): ?array
{
    static $user = false;
    if ($user === false) {
        $user = empty($_SESSION['uid']) ? null
            : one('SELECT id, full_name, email, role FROM users WHERE id = ? AND is_active = 1', [$_SESSION['uid']]);
    }
    return $user;
}

function can(string $perm): bool
{
    $u = current_user();
    return $u !== null && in_array($perm, PERMISSIONS[$u['role']] ?? [], true);
}

function require_login(): void
{
    if (current_user() === null) {
        redirect('login');
    }
}

function require_perm(string $perm): void
{
    require_login();
    if (!can($perm)) {
        http_response_code(403);
        log_action('acces_refuse', 'permission', null, $perm);
        render('error', ['title' => 'Accès refusé', 'message' => "Votre rôle ne permet pas cette action."]);
        exit;
    }
}

function is_locked_out(string $email): bool
{
    $since = date('Y-m-d H:i:s', time() - 60 * (int) config('lockout_minutes', 15));
    $fails = (int) scalar(
        'SELECT COUNT(*) FROM login_attempts WHERE (email = ? OR ip = ?) AND success = 0 AND attempted_at >= ?',
        [$email, client_ip(), $since]
    );
    return $fails >= (int) config('max_login_attempts', 5);
}

function attempt_login(string $email, string $password): bool
{
    $email = mb_strtolower($email);
    if (is_locked_out($email)) {
        log_action('connexion_bloquee', 'user', null, $email);
        return false;
    }
    $u = one('SELECT * FROM users WHERE email = ? AND is_active = 1', [$email]);
    // Vérification à temps constant même si l'utilisateur n'existe pas
    $hash = $u['password_hash'] ?? password_hash('compte-inexistant', PASSWORD_DEFAULT);
    $ok = password_verify($password, $hash) && $u !== null;

    q('INSERT INTO login_attempts (email, ip, success, attempted_at) VALUES (?,?,?,?)', [$email, client_ip(), $ok ? 1 : 0, now()]);

    if (!$ok) {
        log_action('connexion_echec', 'user', $u['id'] ?? null, $email);
        return false;
    }
    if (password_needs_rehash($u['password_hash'], PASSWORD_DEFAULT)) {
        q('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $u['id']]);
    }
    session_regenerate_id(true); // anti-fixation de session
    $_SESSION['uid'] = (int) $u['id'];
    q('UPDATE users SET last_login_at = ? WHERE id = ?', [now(), $u['id']]);
    log_action('connexion', 'user', (int) $u['id']);
    return true;
}

/** Politique de mot de passe : 12 caractères min., majuscule, minuscule, chiffre, symbole. */
function password_policy_errors(string $pwd): array
{
    $errors = [];
    if (mb_strlen($pwd) < 12) $errors[] = '12 caractères minimum';
    if (!preg_match('/[A-Z]/', $pwd)) $errors[] = 'une majuscule';
    if (!preg_match('/[a-z]/', $pwd)) $errors[] = 'une minuscule';
    if (!preg_match('/\d/', $pwd)) $errors[] = 'un chiffre';
    if (!preg_match('/[^A-Za-z0-9]/', $pwd)) $errors[] = 'un caractère spécial';
    return $errors;
}
