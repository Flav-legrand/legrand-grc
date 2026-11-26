<?php
/**
 * Installation de Legrand GRC : création du schéma + données de démonstration.
 *   php database/install.php            → schéma + 93 mesures + comptes + données démo
 *   php database/install.php --no-demo  → schéma + 93 mesures + compte admin seulement
 *   php database/install.php --reset    → supprime les tables existantes avant installation
 * Les mots de passe des comptes sont générés aléatoirement et affichés UNE seule fois.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI uniquement.');
}

require dirname(__DIR__) . '/src/bootstrap.php';

$demo = !in_array('--no-demo', $argv, true);
$reset = in_array('--reset', $argv, true);
$sqlite = str_starts_with(config('db_dsn'), 'sqlite:');
$pdo = db();

$tables = ['requirement_controls', 'risk_controls', 'findings', 'audits', 'risks', 'requirements', 'controls', 'assets', 'audit_log', 'login_attempts', 'users'];
if ($reset) {
    foreach ($tables as $t) {
        $pdo->exec("DROP TABLE IF EXISTS $t");
    }
    echo "Tables supprimées.\n";
}

$schema = file_get_contents(__DIR__ . '/schema.sql');
$schema = str_replace(
    ['{PK}', '{ENGINE}'],
    $sqlite ? ['INTEGER PRIMARY KEY AUTOINCREMENT', ''] : ['INT AUTO_INCREMENT PRIMARY KEY', 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'],
    $schema
);
$schema = preg_replace('/--[^\n]*/', '', $schema);
foreach (array_filter(array_map('trim', explode(';', $schema))) as $stmt) {
    $pdo->exec($stmt);
}
echo "Schéma créé.\n";

$pdo->beginTransaction();

// 1. Mesures ISO 27002:2022
$ins = $pdo->prepare('INSERT INTO controls (ref, theme, title, applicable, justification, status, updated_at) VALUES (?,?,?,1,?,?,?)');
foreach (require __DIR__ . '/controls_27002.php' as [$ref, $theme, $title]) {
    $ins->execute([$ref, $theme, $title, 'Retenue suite à l\'appréciation des risques (à confirmer).', 'non_commence', now()]);
}
echo "93 mesures ISO/IEC 27002:2022 chargées.\n";

// 2. Comptes
$accounts = [['Administrateur GRC', 'admin@legrand-grc.local', 'admin']];
if ($demo) {
    $accounts[] = ['Elyse Flav-Legrand Mongouo', 'rssi@legrand-grc.local', 'rssi'];
    $accounts[] = ['Auditeur interne', 'auditeur@legrand-grc.local', 'auditeur'];
    $accounts[] = ['Direction générale', 'dg@legrand-grc.local', 'lecteur'];
}
echo "\nComptes créés (notez ces mots de passe, ils ne seront plus affichés) :\n";
foreach ($accounts as [$name, $email, $role]) {
    $pwd = 'Lg-' . bin2hex(random_bytes(5)) . '!' . random_int(10, 99) . 'A';
    q('INSERT INTO users (full_name, email, password_hash, role, is_active, created_at) VALUES (?,?,?,?,1,?)',
        [$name, $email, password_hash($pwd, PASSWORD_DEFAULT), $role, now()]);
    printf("  %-10s %-30s %s\n", $role, $email, $pwd);
}

if ($demo) {
    require __DIR__ . '/demo_data.php';
    seed_demo();
    echo "\nDonnées de démonstration (banque fictive) chargées.\n";
}

$pdo->commit();
echo "\nInstallation terminée.\n";
