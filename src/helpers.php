<?php
declare(strict_types=1);

/** Échappement systématique des sorties (protection XSS). */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $page, array $params = []): string
{
    return 'index.php?' . http_build_query(['page' => $page] + $params);
}

function redirect(string $page, array $params = []): never
{
    header('Location: ' . url($page, $params));
    exit;
}

function flash(?string $message = null, string $type = 'ok'): ?array
{
    if ($message !== null) {
        $_SESSION['flash'] = ['msg' => $message, 'type' => $type];
        return null;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function input(string $key, mixed $default = ''): mixed
{
    $v = $_POST[$key] ?? $default;
    return is_string($v) ? trim($v) : $v;
}

function int_between(mixed $v, int $min, int $max, int $default): int
{
    $i = filter_var($v, FILTER_VALIDATE_INT);
    return ($i === false || $i < $min || $i > $max) ? $default : $i;
}

function in_list(mixed $v, array $allowed, string $default): string
{
    return in_array($v, $allowed, true) ? $v : $default;
}

function valid_date(mixed $v): ?string
{
    if (!is_string($v) || $v === '') {
        return null;
    }
    $d = DateTime::createFromFormat('Y-m-d', $v);
    return ($d && $d->format('Y-m-d') === $v) ? $v : null;
}

function render(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $user = current_user();
    $flash = flash();
    ob_start();
    require ROOT . '/src/views/' . $view . '.php';
    $content = ob_get_clean();
    require ROOT . '/src/views/layout.php';
}

/** Libellés d'affichage. */
const LABELS = [
    'status' => [
        'non_commence' => 'Non commencé', 'en_cours' => 'En cours', 'implemente' => 'Implémenté',
        'non_applicable' => 'Non applicable', 'ouvert' => 'Ouvert', 'en_traitement' => 'En traitement',
        'clos' => 'Clos', 'planifie' => 'Planifié', 'termine' => 'Terminé', 'corrige' => 'Corrigé',
        'accepte' => 'Accepté',
    ],
    'compliance' => [
        'a_evaluer' => 'À évaluer', 'conforme' => 'Conforme', 'partiel' => 'Partiellement conforme',
        'non_conforme' => 'Non conforme', 'na' => 'Non applicable',
    ],
    'treatment' => [
        'reduire' => 'Réduire', 'accepter' => 'Accepter', 'transferer' => 'Transférer', 'eviter' => 'Éviter',
    ],
    'severity' => [
        'faible' => 'Faible', 'moyenne' => 'Moyenne', 'elevee' => 'Élevée', 'critique' => 'Critique',
    ],
    'role' => [
        'admin' => 'Administrateur', 'rssi' => 'RSSI', 'auditeur' => 'Auditeur', 'lecteur' => 'Lecteur (Direction)',
    ],
];

function label(string $group, ?string $key): string
{
    return LABELS[$group][$key] ?? (string) $key;
}

function options(string $group, ?string $selected): string
{
    $html = '';
    foreach (LABELS[$group] as $k => $v) {
        $html .= '<option value="' . e($k) . '"' . ($k === $selected ? ' selected' : '') . '>' . e($v) . '</option>';
    }
    return $html;
}

function scale_options(?int $selected, array $labels = ['1 – Faible', '2 – Modéré', '3 – Fort', '4 – Très fort']): string
{
    $html = '';
    foreach ($labels as $i => $l) {
        $n = $i + 1;
        $html .= '<option value="' . $n . '"' . ($n === $selected ? ' selected' : '') . '>' . e($l) . '</option>';
    }
    return $html;
}

function delete_button(string $type, int $id, string $label = 'Supprimer'): string
{
    return '<form method="post" action="' . e(url('delete')) . '" class="inline js-confirm" data-confirm="Confirmer la suppression ?">'
        . csrf_field()
        . '<input type="hidden" name="type" value="' . e($type) . '"><input type="hidden" name="id" value="' . $id . '">'
        . '<button class="btn danger small" type="submit">' . e($label) . '</button></form>';
}

function heatmap(array $m, string $title): string
{
    $html = '<div class="heatmap"><div class="hm-title">' . e($title) . '</div><table class="hm"><tbody>';
    for ($l = 4; $l >= 1; $l--) {
        $html .= '<tr><th class="hm-axis">V' . $l . '</th>';
        for ($i = 1; $i <= 4; $i++) {
            $lvl = risk_level($l * $i);
            $n = $m[$l][$i];
            $html .= '<td class="lvl-' . $lvl . '" title="Vraisemblance ' . $l . ' × Gravité ' . $i . '">' . ($n ?: '') . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '<tr><th></th><th class="hm-axis">G1</th><th class="hm-axis">G2</th><th class="hm-axis">G3</th><th class="hm-axis">G4</th></tr>';
    return $html . '</tbody></table><div class="hm-legend">V = vraisemblance · G = gravité</div></div>';
}

function progress(int $done, int $total): string
{
    $pct = $total ? (int) round(100 * $done / $total) : 0;
    return '<div class="progress" role="progressbar" aria-valuenow="' . $pct . '" aria-valuemin="0" aria-valuemax="100"><span class="w' . (int) (round($pct / 5) * 5) . '"></span></div><small>' . $done . ' / ' . $total . ' (' . $pct . ' %)</small>';
}
