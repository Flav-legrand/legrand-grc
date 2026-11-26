<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';
require ROOT . '/src/controllers.php';

send_security_headers();
start_secure_session();

if (config('force_https') && ($_SERVER['HTTPS'] ?? '') !== 'on' && PHP_SAPI !== 'cli-server') {
    header('Location: https://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '/'), true, 301);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
}

$routes = [
    'login'        => 'page_login',
    'logout'       => 'page_logout',
    'dashboard'    => 'page_dashboard',
    'assets'       => 'page_assets',
    'asset_edit'   => 'page_asset_edit',
    'risks'        => 'page_risks',
    'risk_edit'    => 'page_risk_edit',
    'matrix'       => 'page_matrix',
    'controls'     => 'page_controls',
    'control_edit' => 'page_control_edit',
    'soa_export'   => 'page_soa_export',
    'requirements' => 'page_requirements',
    'requirement_edit' => 'page_requirement_edit',
    'audits'       => 'page_audits',
    'audit_edit'   => 'page_audit_edit',
    'audit_view'   => 'page_audit_view',
    'finding_edit' => 'page_finding_edit',
    'report'       => 'page_report',
    'users'        => 'page_users',
    'journal'      => 'page_journal',
    'delete'       => 'page_delete',
];

$page = $_GET['page'] ?? 'dashboard';
$handler = $routes[$page] ?? null;

try {
    if ($handler === null) {
        http_response_code(404);
        render('error', ['title' => 'Page introuvable', 'message' => "Cette page n'existe pas."]);
    } else {
        $handler();
    }
} catch (Throwable $ex) {
    // Ne jamais afficher la trace à l'utilisateur ; on journalise côté serveur.
    error_log('[Legrand GRC] ' . $ex->getMessage() . ' @ ' . $ex->getFile() . ':' . $ex->getLine());
    http_response_code(500);
    render('error', ['title' => 'Erreur interne', 'message' => "Une erreur est survenue. Elle a été journalisée."]);
}
