<?php
declare(strict_types=1);

/** En-têtes de sécurité HTTP (ISO 27002 — 8.26 Exigences de sécurité des applications). */
function send_security_headers(): void
{
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; frame-ancestors 'none'; form-action 'self'; base-uri 'none'");
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: no-referrer');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header('Cross-Origin-Opener-Policy: same-origin');
    if (config('force_https')) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function start_secure_session(): void
{
    $https = config('force_https') || (($_SERVER['HTTPS'] ?? '') === 'on');
    session_name(config('session_name', 'LGRCSESSID'));
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();

    // Expiration après inactivité
    $limit = (int) config('session_lifetime', 1800);
    if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $limit) {
        session_unset();
        session_destroy();
        session_start();
        flash('Session expirée après inactivité. Veuillez vous reconnecter.', 'warn');
    }
    $_SESSION['last_activity'] = time();
}

/** Jeton anti-CSRF par session. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $sent = $_POST['_csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(400);
        exit('Requête refusée : jeton CSRF invalide.');
    }
}

function client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? 'cli'), 0, 45);
}

/** Journal d'audit applicatif (ISO 27002 — 8.15 Journalisation). */
function log_action(string $action, string $entity, ?int $entityId = null, string $details = ''): void
{
    q('INSERT INTO audit_log (user_id, action, entity, entity_id, details, ip, created_at) VALUES (?,?,?,?,?,?,?)', [
        $_SESSION['uid'] ?? null, $action, $entity, $entityId, mb_substr($details, 0, 500), client_ip(), now(),
    ]);
}
