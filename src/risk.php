<?php
declare(strict_types=1);

/**
 * Méthode de cotation (voir docs/02-methode-analyse-risques.md)
 * Niveau de risque = Vraisemblance (1-4) × Gravité (1-4) → 1 à 16
 */
function risk_score(?int $likelihood, ?int $impact): ?int
{
    return ($likelihood && $impact) ? $likelihood * $impact : null;
}

function risk_level(?int $score): string
{
    return match (true) {
        $score === null => 'na',
        $score >= 12    => 'critique',
        $score >= 8     => 'eleve',
        $score >= 4     => 'moyen',
        default         => 'faible',
    };
}

function risk_level_label(string $level): string
{
    return ['critique' => 'Critique', 'eleve' => 'Élevé', 'moyen' => 'Moyen', 'faible' => 'Faible', 'na' => '—'][$level];
}

function risk_badge(?int $score): string
{
    $lvl = risk_level($score);
    return '<span class="badge lvl-' . $lvl . '">' . ($score ?? '—') . ' · ' . risk_level_label($lvl) . '</span>';
}

/** Matrice 4×4 : nombre de risques par case (brut ou résiduel). */
function risk_matrix(bool $residual = false): array
{
    $l = $residual ? 'residual_likelihood' : 'likelihood';
    $i = $residual ? 'residual_impact' : 'impact';
    $m = array_fill(1, 4, array_fill(1, 4, 0));
    foreach (all("SELECT $l AS l, $i AS i FROM risks WHERE status <> 'clos' AND $l IS NOT NULL AND $i IS NOT NULL") as $r) {
        $m[(int) $r['l']][(int) $r['i']]++;
    }
    return $m;
}

function asset_criticality(array $a): int
{
    return max((int) $a['confidentiality'], (int) $a['integrity'], (int) $a['availability']);
}
