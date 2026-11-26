<?php $title = 'Tableau de bord'; ?>
<header class="page-h"><h1>Tableau de bord SMSI</h1><a class="btn" href="<?= e(url('report')) ?>">Rapport Direction générale</a></header>
<section class="kpis">
  <div class="kpi"><span><?= $assetsCount ?></span>Actifs recensés</div>
  <div class="kpi"><span><?= $riskCount ?></span>Risques ouverts</div>
  <div class="kpi lvl-critique-b"><span><?= $levels['critique'] ?></span>Risques critiques (bruts)</div>
  <div class="kpi"><span><?= $applicable ? round(100 * $implemented / $applicable) : 0 ?> %</span>Mesures implémentées</div>
  <div class="kpi <?= $overdue ? 'lvl-eleve-b' : '' ?>"><span><?= $overdue ?></span>Actions en retard</div>
</section>
<div class="grid2">
  <div class="card"><h2>Cartographie des risques</h2><div class="hm-pair"><?= heatmap($matrix, 'Risque brut') ?><?= heatmap($matrixRes, 'Risque résiduel') ?></div></div>
  <div class="card"><h2>Avancement des mesures ISO 27002 (applicables)</h2>
    <?php foreach ($themes as $t): ?><div class="prog-row"><span><?= e($t['theme']) ?></span><?= progress((int) $t['done'], (int) $t['total']) ?></div><?php endforeach; ?>
    <h2>Conformité RGSSI</h2>
    <ul class="stats"><?php foreach ($reqStats as $s): ?><li><span class="badge c-<?= e($s['compliance']) ?>"><?= e(label('compliance', $s['compliance'])) ?></span> <?= (int) $s['n'] ?></li><?php endforeach; ?></ul>
  </div>
</div>
<div class="grid2">
  <div class="card"><h2>Top 5 des risques</h2>
    <table><thead><tr><th>Réf.</th><th>Risque</th><th>Actif</th><th>Niveau</th></tr></thead><tbody>
    <?php foreach ($topRisks as $r): ?><tr><td><?= e($r['ref']) ?></td><td><?= e($r['title']) ?></td><td><?= e($r['asset_name']) ?></td><td><?= risk_badge((int) $r['likelihood'] * (int) $r['impact']) ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
  <div class="card"><h2>Constats d'audit à traiter</h2>
    <table><thead><tr><th>Constat</th><th>Gravité</th><th>Échéance</th></tr></thead><tbody>
    <?php foreach ($openFindings as $f): ?><tr><td><?= e($f['title']) ?><br><small class="muted"><?= e($f['audit_title']) ?></small></td><td><span class="badge s-<?= e($f['severity']) ?>"><?= e(label('severity', $f['severity'])) ?></span></td><td class="<?= ($f['due_date'] && $f['due_date'] < date('Y-m-d')) ? 'late' : '' ?>"><?= e($f['due_date']) ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
</div>
