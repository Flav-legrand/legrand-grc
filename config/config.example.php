<?php
// Copier ce fichier en config/config.php puis adapter les valeurs.
// Ne jamais versionner config/config.php (voir .gitignore).
return [
    'app_name'     => 'Legrand GRC',
    'organisation' => 'Banque Mayombe (organisation fictive de démonstration)',

    // MySQL (production / docker-compose)
    'db_dsn'  => getenv('DB_DSN') ?: 'mysql:host=127.0.0.1;port=3306;dbname=legrand_grc;charset=utf8mb4',
    'db_user' => getenv('DB_USER') ?: 'legrand',
    'db_pass' => getenv('DB_PASS') ?: 'changer-ce-mot-de-passe',

    // SQLite (démo rapide sans serveur MySQL) :
    // 'db_dsn' => 'sqlite:' . __DIR__ . '/../database/legrand_grc.sqlite',

    // Sécurité
    'session_name'      => 'LGRCSESSID',
    'session_lifetime'  => 1800,  // déconnexion après 30 min d'inactivité
    'max_login_attempts'=> 5,     // verrouillage après 5 échecs...
    'lockout_minutes'   => 15,    // ...sur une fenêtre de 15 minutes
    'force_https'       => (bool) (getenv('FORCE_HTTPS') ?: false),
    'timezone'          => 'Africa/Brazzaville',
];
