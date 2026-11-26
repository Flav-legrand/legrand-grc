<?php
// Tests unitaires minimalistes (sans dépendance) : php tests/run.php
declare(strict_types=1);
require dirname(__DIR__) . '/src/bootstrap.php';

$fail = 0; $n = 0;
function check(string $name, bool $ok): void { global $fail, $n; $n++; if (!$ok) { $fail++; echo "ÉCHEC : $name\n"; } }

check('score 3×4 = 12', risk_score(3, 4) === 12);
check('score nul si résiduel absent', risk_score(null, 3) === null);
check('12 → critique', risk_level(12) === 'critique');
check('9 → élevé', risk_level(9) === 'eleve');
check('8 → élevé', risk_level(8) === 'eleve');
check('6 → moyen', risk_level(6) === 'moyen');
check('3 → faible', risk_level(3) === 'faible');
check('échappement XSS', e('<script>"\'') === '&lt;script&gt;&quot;&#039;');
check('int_between hors borne → défaut', int_between('9', 1, 4, 2) === 2);
check('int_between injection → défaut', int_between('1 OR 1=1', 1, 4, 2) === 2);
check('in_list liste blanche', in_list('admin', ['lecteur'], 'lecteur') === 'lecteur');
check('date invalide rejetée', valid_date('2026-13-45') === null);
check('date valide acceptée', valid_date('2026-11-26') === '2026-11-26');
check('mot de passe faible refusé', count(password_policy_errors('azerty')) >= 3);
check('mot de passe robuste accepté', password_policy_errors('Brazza-2026!Secure') === []);
check('criticité = max(C,I,D)', asset_criticality(['confidentiality' => 1, 'integrity' => 4, 'availability' => 2]) === 4);

echo "$n tests, $fail échec(s)\n";
exit($fail ? 1 : 0);
