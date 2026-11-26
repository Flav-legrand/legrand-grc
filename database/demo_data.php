<?php
/** Données de démonstration : banque fictive « Banque Mayombe » à Brazzaville. */
declare(strict_types=1);

function seed_demo(): void
{
    $ctl = array_column(all('SELECT id, ref FROM controls'), 'id', 'ref');
    $d = fn(int $days) => date('Y-m-d', strtotime(($days >= 0 ? '+' : '') . $days . ' days'));

    // --- Actifs ---------------------------------------------------------
    $assets = [
        ['Octroi de crédit et gestion des comptes', 'Processus métier', 'Directeur des opérations', 'Processus cœur de la banque', 3, 4, 4],
        ['Données clients (KYC, comptes, transactions)', 'Information', 'Responsable conformité', 'Données personnelles et bancaires', 4, 4, 3],
        ['Core banking', 'Application', 'DSI', 'Système central de tenue de comptes', 4, 4, 4],
        ['Passerelle API Mobile Money', 'Application', 'Chef de projet digital', 'Interconnexion avec les opérateurs de Mobile Money', 3, 4, 4],
        ['Banque en ligne (web et mobile)', 'Application', 'Chef de projet digital', 'Canal client exposé sur Internet', 3, 4, 3],
        ['Annuaire Active Directory', 'Serveur', 'Administrateur systèmes', 'Gestion centralisée des identités', 3, 4, 4],
        ['Salle serveurs du siège (Brazzaville)', 'Site', 'Responsable infrastructure', 'Hébergement principal', 2, 3, 4],
        ['Sauvegardes', 'Serveur', 'Administrateur systèmes', 'Sauvegardes quotidiennes du core banking', 4, 4, 3],
        ['Postes de travail des agences', 'Poste de travail', 'Responsable support', 'Environ 120 postes Windows', 2, 3, 3],
        ['Liaison inter-agences (Brazzaville – Pointe-Noire)', 'Réseau', 'Responsable réseau', 'VPN MPLS entre sites', 2, 3, 4],
        ['Prestataire d\'infogérance', 'Fournisseur', 'DSI', 'Maintenance à distance des serveurs', 3, 3, 3],
        ['Personnel des agences', 'Personne', 'DRH', 'Guichetiers et conseillers', 2, 2, 3],
    ];
    $assetId = [];
    foreach ($assets as $a) {
        q('INSERT INTO assets (name, category, owner, description, confidentiality, integrity, availability, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?)', [...$a, now(), now()]);
        $assetId[$a[0]] = (int) db()->lastInsertId();
    }

    // --- Risques --------------------------------------------------------
    // [ref, titre, actif, menace, vulnérabilité, conséquence, V, G, traitement, Vr, Gr, propriétaire, statut, échéance (jours), mesures]
    $risks = [
        ['R-001', 'Rançongiciel chiffrant le core banking et les sauvegardes', 'Core banking', 'Groupe cybercriminel (rançongiciel)', 'Sauvegardes accessibles depuis le domaine, absence d\'EDR', 'Arrêt des opérations, perte de données, atteinte à l\'image', 3, 4, 'reduire', 2, 3, 'DSI', 'en_traitement', 45, ['8.7', '8.13', '8.16', '5.24', '5.30']],
        ['R-002', 'Hameçonnage ciblé des agents menant au vol d\'identifiants', 'Personnel des agences', 'Attaquant externe', 'Faible sensibilisation, MFA absent sur la messagerie', 'Prise de contrôle de comptes, fraude', 4, 3, 'reduire', 2, 3, 'RSSI', 'en_traitement', 30, ['6.3', '8.5', '8.23', '5.17']],
        ['R-003', 'Fraude interne par abus de droits sur les comptes clients', 'Données clients (KYC, comptes, transactions)', 'Employé malveillant', 'Droits excessifs, pas de revue périodique des habilitations', 'Pertes financières, sanctions', 2, 4, 'reduire', 1, 4, 'Directeur des opérations', 'ouvert', 60, ['5.3', '5.18', '8.2', '8.15']],
        ['R-004', 'Compromission de la passerelle API Mobile Money', 'Passerelle API Mobile Money', 'Attaquant externe', 'Clés API statiques, absence de limitation de débit', 'Transactions frauduleuses, fuite de données', 3, 4, 'reduire', 1, 3, 'Chef de projet digital', 'en_traitement', 30, ['8.24', '8.26', '8.28', '8.29', '5.14']],
        ['R-005', 'Indisponibilité prolongée due aux coupures électriques', 'Salle serveurs du siège (Brazzaville)', 'Délestage / panne du réseau électrique', 'Autonomie onduleur limitée, groupe électrogène non testé', 'Interruption de service en agences', 4, 3, 'reduire', 2, 2, 'Responsable infrastructure', 'en_traitement', 20, ['7.11', '7.13', '8.14', '5.30']],
        ['R-006', 'Fuite de données personnelles des clients', 'Données clients (KYC, comptes, transactions)', 'Erreur interne ou attaquant', 'Données non chiffrées en base, exports Excel non maîtrisés', 'Atteinte à la vie privée, sanction du régulateur', 3, 4, 'reduire', 2, 3, 'Responsable conformité', 'ouvert', 90, ['5.12', '5.34', '8.11', '8.12', '8.24']],
        ['R-007', 'Déni de service sur la banque en ligne', 'Banque en ligne (web et mobile)', 'Hacktivistes / concurrence', 'Pas de protection anti-DDoS ni de WAF', 'Indisponibilité du canal digital', 2, 3, 'transferer', 1, 3, 'Chef de projet digital', 'ouvert', 120, ['8.20', '8.21', '8.6']],
        ['R-008', 'Prise de contrôle de l\'Active Directory', 'Annuaire Active Directory', 'Attaquant ayant un premier accès', 'Comptes administrateurs partagés, mots de passe faibles', 'Compromission de tout le SI', 3, 4, 'reduire', 2, 4, 'Administrateur systèmes', 'en_traitement', 30, ['8.2', '8.5', '5.16', '8.18']],
        ['R-009', 'Accès à distance non maîtrisé du prestataire', 'Prestataire d\'infogérance', 'Prestataire compromis', 'Accès VPN permanent sans traçabilité', 'Rebond vers le SI interne', 2, 4, 'reduire', 1, 3, 'DSI', 'ouvert', 60, ['5.19', '5.20', '5.22', '8.15']],
        ['R-010', 'Non-conformité au RGSSI lors d\'un contrôle de l\'ANSSI', null, 'Contrôle réglementaire', 'Démarche de conformité non formalisée', 'Mise en demeure, atteinte à la réputation', 3, 3, 'reduire', 1, 2, 'RSSI', 'en_traitement', 150, ['5.31', '5.36', '5.35', '5.1']],
        ['R-011', 'Vulnérabilités non corrigées sur les postes des agences', 'Postes de travail des agences', 'Logiciel malveillant', 'Correctifs appliqués manuellement et irrégulièrement', 'Propagation de logiciels malveillants', 3, 3, 'reduire', 2, 2, 'Responsable support', 'en_traitement', 45, ['8.8', '8.1', '8.7', '8.9']],
        ['R-012', 'Interception du trafic inter-agences', 'Liaison inter-agences (Brazzaville – Pointe-Noire)', 'Attaquant sur le réseau opérateur', 'Protocoles internes non chiffrés', 'Atteinte à la confidentialité des transactions', 1, 3, 'accepter', 1, 3, 'Responsable réseau', 'ouvert', null, ['8.20', '8.22', '8.24']],
    ];
    foreach ($risks as $r) {
        q('INSERT INTO risks (ref, title, asset_id, threat, vulnerability, consequence, likelihood, impact, treatment, residual_likelihood, residual_impact, owner, status, due_date, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [
            $r[0], $r[1], $r[2] ? $assetId[$r[2]] : null, $r[3], $r[4], $r[5], $r[6], $r[7], $r[8], $r[9], $r[10], $r[11], $r[12],
            $r[13] === null ? null : $d($r[13]), now(), now(),
        ]);
        $rid = (int) db()->lastInsertId();
        foreach ($r[14] as $ref) {
            q('INSERT INTO risk_controls (risk_id, control_id) VALUES (?,?)', [$rid, $ctl[$ref]]);
        }
    }

    // --- État de mise en œuvre de quelques mesures ----------------------
    $impl = ['5.1', '5.2', '5.9', '5.15', '6.6', '7.1', '7.2', '7.4', '8.7', '8.13', '8.17', '8.20'];
    $prog = ['5.12', '5.18', '5.24', '6.3', '7.11', '8.2', '8.5', '8.8', '8.15', '8.16', '8.24', '8.26', '8.28', '5.31'];
    foreach ($impl as $ref) q("UPDATE controls SET status='implemente', owner='RSSI', evidence='Voir docs/01-PSSI.md' WHERE ref=?", [$ref]);
    foreach ($prog as $ref) q("UPDATE controls SET status='en_cours', owner='RSSI' WHERE ref=?", [$ref]);
    q("UPDATE controls SET applicable=0, status='non_applicable', justification='Aucun développement externalisé : les applications sont développées en interne.' WHERE ref='8.30'");
    q("UPDATE controls SET applicable=0, status='non_applicable', justification='Pas de travail à distance autorisé pour le personnel à ce stade (à réévaluer).' WHERE ref='6.7'");

    // --- Trame d'exigences RGSSI (PROVISOIRE, à remplacer par le texte officiel) ---
    $reqs = [
        ['RGSSI-GOV (provisoire)', 'Gouvernance', 'Désigner un responsable de la sécurité des SI et formaliser une politique de sécurité approuvée par la direction', 'partiel', ['5.1', '5.2', '5.4']],
        ['RGSSI-RSK (provisoire)', 'Gestion des risques', 'Mener et mettre à jour une analyse des risques sur les systèmes d\'information', 'partiel', ['5.9', '5.12']],
        ['RGSSI-ACT (provisoire)', 'Gestion des actifs', 'Tenir un inventaire à jour des actifs et les classifier', 'conforme', ['5.9', '5.12', '5.13']],
        ['RGSSI-IAM (provisoire)', 'Contrôle d\'accès', 'Gérer les identités, les habilitations et les accès privilégiés', 'partiel', ['5.15', '5.16', '5.18', '8.2', '8.5']],
        ['RGSSI-CRY (provisoire)', 'Cryptographie', 'Protéger les données sensibles par des mécanismes cryptographiques adaptés', 'non_conforme', ['8.24']],
        ['RGSSI-PHY (provisoire)', 'Sécurité physique', 'Protéger les locaux techniques et les équipements', 'conforme', ['7.1', '7.2', '7.4', '7.11']],
        ['RGSSI-EXP (provisoire)', 'Sécurité de l\'exploitation', 'Gérer les vulnérabilités, les sauvegardes et la protection antivirale', 'partiel', ['8.7', '8.8', '8.13']],
        ['RGSSI-LOG (provisoire)', 'Journalisation et détection', 'Journaliser les événements de sécurité et superviser les systèmes', 'partiel', ['8.15', '8.16', '8.17']],
        ['RGSSI-RES (provisoire)', 'Sécurité des réseaux', 'Cloisonner et filtrer les réseaux', 'partiel', ['8.20', '8.21', '8.22']],
        ['RGSSI-DEV (provisoire)', 'Développement sécurisé', 'Intégrer la sécurité dans le cycle de vie des projets et des applications', 'non_conforme', ['5.8', '8.25', '8.26', '8.28', '8.29']],
        ['RGSSI-FOU (provisoire)', 'Relations fournisseurs', 'Encadrer la sécurité dans les contrats et l\'accès des prestataires', 'non_conforme', ['5.19', '5.20', '5.22']],
        ['RGSSI-INC (provisoire)', 'Gestion des incidents', 'Détecter, traiter et déclarer les incidents de sécurité aux autorités compétentes', 'partiel', ['5.24', '5.25', '5.26', '5.5', '6.8']],
        ['RGSSI-PCA (provisoire)', 'Continuité d\'activité', 'Disposer de plans de continuité et de reprise testés', 'non_conforme', ['5.29', '5.30', '8.14']],
        ['RGSSI-SEN (provisoire)', 'Sensibilisation', 'Sensibiliser et former le personnel à la sécurité', 'partiel', ['6.3']],
        ['RGSSI-AUD (provisoire)', 'Audit et contrôle', 'Faire réaliser des audits de sécurité (dont par des prestataires agréés) et suivre les plans d\'action', 'a_evaluer', ['5.35', '5.36']],
    ];
    foreach ($reqs as [$ref, $domain, $title, $comp, $ctrls]) {
        q('INSERT INTO requirements (framework, ref, domain, title, compliance, comment, updated_at) VALUES (?,?,?,?,?,?,?)',
            ['RGSSI', $ref, $domain, $title, $comp, 'Libellé provisoire : à aligner sur le texte officiel de l\'ANSSI Congo.', now()]);
        $rid = (int) db()->lastInsertId();
        foreach ($ctrls as $c) q('INSERT INTO requirement_controls (requirement_id, control_id) VALUES (?,?)', [$rid, $ctl[$c]]);
    }

    // --- Audits et constats --------------------------------------------
    q('INSERT INTO audits (title, type, scope, auditor, start_date, end_date, status, created_at) VALUES (?,?,?,?,?,?,?,?)',
        ["Test d'intrusion de la banque en ligne et de l'API Mobile Money", "Test d'intrusion", 'Applications web/mobile exposées sur Internet et passerelle API', 'Prestataire externe (fictif)', $d(-40), $d(-30), 'termine', now()]);
    $a1 = (int) db()->lastInsertId();
    $f1 = [
        ['Injection SQL sur le formulaire de recherche de transactions', 'critique', '8.28', 'Utiliser des requêtes préparées, valider les entrées, relancer un test de non-régression.', 'Chef de projet digital', $d(-5), 'en_cours'],
        ['Absence de limitation de débit sur l\'authentification API', 'elevee', '8.26', 'Mettre en place un rate limiting et un verrouillage progressif des comptes.', 'Chef de projet digital', $d(10), 'ouvert'],
        ['En-têtes de sécurité HTTP absents', 'moyenne', '8.26', 'Ajouter CSP, HSTS, X-Frame-Options, X-Content-Type-Options.', 'Équipe web', $d(-12), 'corrige'],
        ['Version de serveur web divulguée', 'faible', '8.9', 'Masquer les bannières de version.', 'Administrateur systèmes', $d(15), 'ouvert'],
    ];
    q('INSERT INTO audits (title, type, scope, auditor, start_date, end_date, status, created_at) VALUES (?,?,?,?,?,?,?,?)',
        ['Audit interne de conformité ISO 27001 / RGSSI', 'Audit interne', 'Siège de Brazzaville : gouvernance, contrôle d\'accès, sauvegardes, continuité', 'Auditeur interne', $d(-10), null, 'en_cours', now()]);
    $a2 = (int) db()->lastInsertId();
    $f2 = [
        ['Revue des habilitations non réalisée depuis plus d\'un an', 'elevee', '5.18', 'Organiser une revue semestrielle des droits validée par les responsables métiers.', 'DRH / DSI', $d(30), 'ouvert'],
        ['Restauration des sauvegardes jamais testée', 'critique', '8.13', 'Planifier un test de restauration trimestriel avec procès-verbal.', 'Administrateur systèmes', $d(-2), 'ouvert'],
        ['PCA/PRA non formalisé', 'elevee', '5.30', 'Rédiger et tester le PCA/PRA (voir docs/03-PCA-PRA.md).', 'RSSI', $d(60), 'en_cours'],
    ];
    foreach ([[$a1, $f1], [$a2, $f2]] as [$aid, $list]) {
        foreach ($list as $f) {
            q('INSERT INTO findings (audit_id, title, severity, control_id, recommendation, action_owner, due_date, status, created_at) VALUES (?,?,?,?,?,?,?,?,?)',
                [$aid, $f[0], $f[1], $ctl[$f[2]], $f[3], $f[4], $f[5], $f[6], now()]);
        }
    }
}
