<?php $title = 'Cartographie des risques'; ?>
<h1>Cartographie des risques</h1>
<div class="card"><div class="hm-pair big"><?= heatmap($matrix, 'Risque brut (avant traitement)') ?><?= heatmap($matrixRes, 'Risque résiduel (après mesures)') ?></div>
<div class="legend"><span class="badge lvl-faible">1–3 Faible</span> <span class="badge lvl-moyen">4–6 Moyen</span> <span class="badge lvl-eleve">8–9 Élevé</span> <span class="badge lvl-critique">12–16 Critique</span></div>
<p class="muted">Seuil d'acceptation proposé à la Direction générale : un risque résiduel ≥ 8 nécessite un plan de traitement validé ; ≥ 12, une décision formelle de la DG.</p></div>
