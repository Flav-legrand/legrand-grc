<?php
declare(strict_types=1);

const ASSET_CATEGORIES = ['Processus métier', 'Information', 'Application', 'Serveur', 'Poste de travail', 'Réseau', 'Personne', 'Site', 'Fournisseur'];
const AUDIT_TYPES = ['Audit interne', 'Audit externe (PASSI)', "Test d'intrusion", 'Contrôle de conformité ANSSI', 'Audit ISO 27001'];

/* ---------- Authentification ---------- */

function page_login(): void
{
    if (current_user()) {
        redirect('dashboard');
    }
    $error = null;
    $email = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = (string) input('email');
        if (attempt_login($email, (string) ($_POST['password'] ?? ''))) {
            redirect('dashboard');
        }
        $error = is_locked_out(mb_strtolower($email))
            ? 'Trop de tentatives. Compte temporairement verrouillé (' . (int) config('lockout_minutes') . ' min).'
            : 'Identifiants invalides.';
    }
    render('login', ['error' => $error, 'email' => $email]);
}

function page_logout(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        log_action('deconnexion', 'user', $_SESSION['uid'] ?? null);
        $_SESSION = [];
        session_destroy();
    }
    redirect('login');
}

/* ---------- Tableau de bord ---------- */

function page_dashboard(): void
{
    require_perm('read');
    $risks = all("SELECT * FROM risks WHERE status <> 'clos'");
    $levels = ['critique' => 0, 'eleve' => 0, 'moyen' => 0, 'faible' => 0];
    $resLevels = $levels;
    foreach ($risks as $r) {
        $levels[risk_level(risk_score((int) $r['likelihood'], (int) $r['impact']))]++;
        $rs = risk_score($r['residual_likelihood'] ? (int) $r['residual_likelihood'] : null, $r['residual_impact'] ? (int) $r['residual_impact'] : null);
        if ($rs !== null) $resLevels[risk_level($rs)]++;
    }
    $applicable = (int) scalar('SELECT COUNT(*) FROM controls WHERE applicable = 1');
    $implemented = (int) scalar("SELECT COUNT(*) FROM controls WHERE applicable = 1 AND status = 'implemente'");
    $themes = all("SELECT theme, SUM(CASE WHEN status='implemente' THEN 1 ELSE 0 END) AS done, COUNT(*) AS total
                   FROM controls WHERE applicable = 1 GROUP BY theme ORDER BY theme");
    $reqStats = all('SELECT compliance, COUNT(*) AS n FROM requirements GROUP BY compliance');
    $openFindings = all("SELECT f.*, a.title AS audit_title FROM findings f JOIN audits a ON a.id = f.audit_id
                         WHERE f.status IN ('ouvert','en_cours') ORDER BY CASE f.severity WHEN 'critique' THEN 1 WHEN 'elevee' THEN 2 WHEN 'moyenne' THEN 3 ELSE 4 END, f.due_date LIMIT 6");
    $overdue = (int) scalar("SELECT COUNT(*) FROM findings WHERE status IN ('ouvert','en_cours') AND due_date IS NOT NULL AND due_date < ?", [date('Y-m-d')]);
    $topRisks = all("SELECT r.*, a.name AS asset_name FROM risks r LEFT JOIN assets a ON a.id = r.asset_id
                     WHERE r.status <> 'clos' ORDER BY (r.likelihood * r.impact) DESC, r.ref LIMIT 5");
    render('dashboard', compact('levels', 'resLevels', 'applicable', 'implemented', 'themes', 'reqStats', 'openFindings', 'overdue', 'topRisks') + [
        'assetsCount' => (int) scalar('SELECT COUNT(*) FROM assets'),
        'riskCount'   => count($risks),
        'matrix'      => risk_matrix(false),
        'matrixRes'   => risk_matrix(true),
    ]);
}

/* ---------- Actifs ---------- */

function page_assets(): void
{
    require_perm('read');
    $assets = all('SELECT a.*, (SELECT COUNT(*) FROM risks r WHERE r.asset_id = a.id) AS risk_count FROM assets a ORDER BY a.category, a.name');
    render('assets', ['assets' => $assets]);
}

function page_asset_edit(): void
{
    require_perm('write');
    $id = (int) ($_GET['id'] ?? 0);
    $asset = $id ? one('SELECT * FROM assets WHERE id = ?', [$id]) : null;
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'name'  => mb_substr((string) input('name'), 0, 150),
            'category' => in_list(input('category'), ASSET_CATEGORIES, 'Information'),
            'owner' => mb_substr((string) input('owner'), 0, 120),
            'description' => mb_substr((string) input('description'), 0, 2000),
            'confidentiality' => int_between(input('confidentiality'), 1, 4, 2),
            'integrity' => int_between(input('integrity'), 1, 4, 2),
            'availability' => int_between(input('availability'), 1, 4, 2),
        ];
        if ($data['name'] === '') $errors[] = 'Le nom est obligatoire.';
        if ($data['owner'] === '') $errors[] = 'Le propriétaire est obligatoire.';
        if (!$errors) {
            if ($asset) {
                q('UPDATE assets SET name=?, category=?, owner=?, description=?, confidentiality=?, integrity=?, availability=?, updated_at=? WHERE id=?',
                    [...array_values($data), now(), $id]);
                log_action('modification', 'asset', $id, $data['name']);
            } else {
                q('INSERT INTO assets (name, category, owner, description, confidentiality, integrity, availability, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?)',
                    [...array_values($data), now(), now()]);
                $id = (int) db()->lastInsertId();
                log_action('creation', 'asset', $id, $data['name']);
            }
            flash('Actif enregistré.');
            redirect('assets');
        }
        $asset = $data + ['id' => $id];
    }
    render('asset_form', ['asset' => $asset, 'errors' => $errors]);
}

/* ---------- Risques ---------- */

function page_risks(): void
{
    require_perm('read');
    $filter = in_list($_GET['level'] ?? '', ['critique', 'eleve', 'moyen', 'faible'], '');
    $risks = all('SELECT r.*, a.name AS asset_name FROM risks r LEFT JOIN assets a ON a.id = r.asset_id ORDER BY (r.likelihood * r.impact) DESC, r.ref');
    if ($filter) {
        $risks = array_values(array_filter($risks, fn($r) => risk_level(risk_score((int) $r['likelihood'], (int) $r['impact'])) === $filter));
    }
    render('risks', ['risks' => $risks, 'filter' => $filter]);
}

function page_risk_edit(): void
{
    require_perm('write');
    $id = (int) ($_GET['id'] ?? 0);
    $risk = $id ? one('SELECT * FROM risks WHERE id = ?', [$id]) : null;
    $linked = $id ? array_map('intval', array_column(all('SELECT control_id FROM risk_controls WHERE risk_id = ?', [$id]), 'control_id')) : [];
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $assetId = (int) input('asset_id', 0);
        $data = [
            'ref'   => mb_substr((string) input('ref'), 0, 20),
            'title' => mb_substr((string) input('title'), 0, 200),
            'asset_id' => ($assetId && one('SELECT id FROM assets WHERE id = ?', [$assetId])) ? $assetId : null,
            'threat' => mb_substr((string) input('threat'), 0, 300),
            'vulnerability' => mb_substr((string) input('vulnerability'), 0, 300),
            'consequence' => mb_substr((string) input('consequence'), 0, 300),
            'likelihood' => int_between(input('likelihood'), 1, 4, 2),
            'impact' => int_between(input('impact'), 1, 4, 2),
            'treatment' => in_list(input('treatment'), array_keys(LABELS['treatment']), 'reduire'),
            'residual_likelihood' => int_between(input('residual_likelihood'), 1, 4, 0) ?: null,
            'residual_impact' => int_between(input('residual_impact'), 1, 4, 0) ?: null,
            'owner' => mb_substr((string) input('owner'), 0, 120),
            'status' => in_list(input('status'), ['ouvert', 'en_traitement', 'clos'], 'ouvert'),
            'due_date' => valid_date(input('due_date')),
        ];
        $controlIds = array_map('intval', (array) ($_POST['controls'] ?? []));
        if ($data['ref'] === '' || $data['title'] === '') $errors[] = 'Référence et intitulé obligatoires.';
        if ($data['threat'] === '' || $data['vulnerability'] === '') $errors[] = 'Menace et vulnérabilité obligatoires.';
        if ($data['owner'] === '') $errors[] = 'Le propriétaire du risque est obligatoire.';
        $dup = one('SELECT id FROM risks WHERE ref = ? AND id <> ?', [$data['ref'], $id]);
        if ($dup) $errors[] = 'Cette référence existe déjà.';
        if (!$errors) {
            db()->beginTransaction();
            if ($risk) {
                q('UPDATE risks SET ref=?, title=?, asset_id=?, threat=?, vulnerability=?, consequence=?, likelihood=?, impact=?, treatment=?, residual_likelihood=?, residual_impact=?, owner=?, status=?, due_date=?, updated_at=? WHERE id=?',
                    [...array_values($data), now(), $id]);
                log_action('modification', 'risk', $id, $data['ref']);
            } else {
                q('INSERT INTO risks (ref, title, asset_id, threat, vulnerability, consequence, likelihood, impact, treatment, residual_likelihood, residual_impact, owner, status, due_date, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
                    [...array_values($data), now(), now()]);
                $id = (int) db()->lastInsertId();
                log_action('creation', 'risk', $id, $data['ref']);
            }
            q('DELETE FROM risk_controls WHERE risk_id = ?', [$id]);
            foreach (array_unique($controlIds) as $cid) {
                if (one('SELECT id FROM controls WHERE id = ?', [$cid])) {
                    q('INSERT INTO risk_controls (risk_id, control_id) VALUES (?,?)', [$id, $cid]);
                }
            }
            db()->commit();
            flash('Risque enregistré.');
            redirect('risks');
        }
        $risk = $data + ['id' => $id];
        $linked = $controlIds;
    }
    if (!$risk) {
        $next = (int) scalar('SELECT COUNT(*) FROM risks') + 1;
        $risk = ['ref' => sprintf('R-%03d', $next)];
    }
    render('risk_form', [
        'risk' => $risk, 'errors' => $errors, 'linked' => $linked,
        'assets' => all('SELECT id, name, category FROM assets ORDER BY name'),
        'controls' => all('SELECT id, ref, title FROM controls ORDER BY id'),
    ]);
}

function page_matrix(): void
{
    require_perm('read');
    render('matrix', ['matrix' => risk_matrix(false), 'matrixRes' => risk_matrix(true)]);
}

/* ---------- Mesures ISO 27002 / SoA ---------- */

function page_controls(): void
{
    require_perm('read');
    $theme = in_list($_GET['theme'] ?? '', ['Organisationnelle', 'Humaine', 'Physique', 'Technologique'], '');
    $sql = 'SELECT c.*, (SELECT COUNT(*) FROM risk_controls rc WHERE rc.control_id = c.id) AS risk_count FROM controls c';
    $controls = $theme ? all($sql . ' WHERE theme = ? ORDER BY c.id', [$theme]) : all($sql . ' ORDER BY c.id');
    render('controls', ['controls' => $controls, 'theme' => $theme]);
}

function page_control_edit(): void
{
    require_perm('write');
    $id = (int) ($_GET['id'] ?? 0);
    $c = one('SELECT * FROM controls WHERE id = ?', [$id]);
    if (!$c) redirect('controls');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $applicable = input('applicable') === '1' ? 1 : 0;
        $status = $applicable ? in_list(input('status'), ['non_commence', 'en_cours', 'implemente'], 'non_commence') : 'non_applicable';
        $just = mb_substr((string) input('justification'), 0, 500);
        if ($just === '') {
            $errors = ["La justification est obligatoire dans la déclaration d'applicabilité (ISO 27001 §6.1.3 d)."];
            render('control_form', ['c' => array_merge($c, $_POST), 'errors' => $errors]);
            return;
        }
        q('UPDATE controls SET applicable=?, justification=?, status=?, owner=?, evidence=?, updated_at=? WHERE id=?', [
            $applicable, $just, $status, mb_substr((string) input('owner'), 0, 120), mb_substr((string) input('evidence'), 0, 500), now(), $id,
        ]);
        log_action('modification', 'control', $id, $c['ref'] . ' → ' . $status);
        flash('Mesure ' . $c['ref'] . ' mise à jour.');
        redirect('controls', ['theme' => $c['theme']]);
    }
    render('control_form', ['c' => $c, 'errors' => []]);
}

/** Export CSV de la déclaration d'applicabilité (protection contre l'injection de formules). */
function page_soa_export(): void
{
    require_perm('read');
    log_action('export', 'soa');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="declaration-applicabilite-' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // BOM pour Excel
    $safe = fn($v) => preg_match('/^[=+\-@\t\r]/', (string) $v) ? "'" . $v : (string) $v;
    fputcsv($out, ['Référence', 'Thème', 'Mesure', 'Applicable', 'Justification', 'Statut', 'Responsable', 'Preuves'], ';');
    foreach (all('SELECT * FROM controls ORDER BY id') as $c) {
        fputcsv($out, array_map($safe, [$c['ref'], $c['theme'], $c['title'], $c['applicable'] ? 'Oui' : 'Non',
            $c['justification'], label('status', $c['status']), $c['owner'], $c['evidence']]), ';');
    }
    fclose($out);
    exit;
}

/* ---------- Exigences RGSSI ---------- */

function page_requirements(): void
{
    require_perm('read');
    $reqs = all('SELECT * FROM requirements ORDER BY framework, ref');
    $map = [];
    foreach (all('SELECT rc.requirement_id, c.ref FROM requirement_controls rc JOIN controls c ON c.id = rc.control_id ORDER BY c.id') as $row) {
        $map[$row['requirement_id']][] = $row['ref'];
    }
    render('requirements', ['reqs' => $reqs, 'map' => $map]);
}

function page_requirement_edit(): void
{
    require_perm('write');
    $id = (int) ($_GET['id'] ?? 0);
    $req = $id ? one('SELECT * FROM requirements WHERE id = ?', [$id]) : null;
    $linked = $id ? array_map('intval', array_column(all('SELECT control_id FROM requirement_controls WHERE requirement_id = ?', [$id]), 'control_id')) : [];
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'framework' => mb_substr((string) input('framework', 'RGSSI'), 0, 30) ?: 'RGSSI',
            'ref' => mb_substr((string) input('ref'), 0, 30),
            'domain' => mb_substr((string) input('domain'), 0, 80),
            'title' => mb_substr((string) input('title'), 0, 300),
            'compliance' => in_list(input('compliance'), array_keys(LABELS['compliance']), 'a_evaluer'),
            'comment' => mb_substr((string) input('comment'), 0, 500),
        ];
        $controlIds = array_map('intval', (array) ($_POST['controls'] ?? []));
        if ($data['ref'] === '' || $data['title'] === '' || $data['domain'] === '') $errors[] = 'Référence, domaine et intitulé obligatoires.';
        if (!$errors) {
            db()->beginTransaction();
            if ($req) {
                q('UPDATE requirements SET framework=?, ref=?, domain=?, title=?, compliance=?, comment=?, updated_at=? WHERE id=?', [...array_values($data), now(), $id]);
                log_action('modification', 'requirement', $id, $data['ref'] . ' → ' . $data['compliance']);
            } else {
                q('INSERT INTO requirements (framework, ref, domain, title, compliance, comment, updated_at) VALUES (?,?,?,?,?,?,?)', [...array_values($data), now()]);
                $id = (int) db()->lastInsertId();
                log_action('creation', 'requirement', $id, $data['ref']);
            }
            q('DELETE FROM requirement_controls WHERE requirement_id = ?', [$id]);
            foreach (array_unique($controlIds) as $cid) {
                if (one('SELECT id FROM controls WHERE id = ?', [$cid])) {
                    q('INSERT INTO requirement_controls (requirement_id, control_id) VALUES (?,?)', [$id, $cid]);
                }
            }
            db()->commit();
            flash('Exigence enregistrée.');
            redirect('requirements');
        }
        $req = $data + ['id' => $id];
        $linked = $controlIds;
    }
    render('requirement_form', ['req' => $req, 'errors' => $errors, 'linked' => $linked, 'controls' => all('SELECT id, ref, title FROM controls ORDER BY id')]);
}

/* ---------- Audits et constats ---------- */

function page_audits(): void
{
    require_perm('read');
    $audits = all("SELECT a.*, (SELECT COUNT(*) FROM findings f WHERE f.audit_id = a.id) AS nb,
                   (SELECT COUNT(*) FROM findings f WHERE f.audit_id = a.id AND f.status IN ('ouvert','en_cours')) AS open_nb
                   FROM audits a ORDER BY a.start_date DESC");
    render('audits', ['audits' => $audits]);
}

function page_audit_edit(): void
{
    require_perm('audit_write');
    $id = (int) ($_GET['id'] ?? 0);
    $audit = $id ? one('SELECT * FROM audits WHERE id = ?', [$id]) : null;
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'title' => mb_substr((string) input('title'), 0, 200),
            'type' => in_list(input('type'), AUDIT_TYPES, 'Audit interne'),
            'scope' => mb_substr((string) input('scope'), 0, 500),
            'auditor' => mb_substr((string) input('auditor'), 0, 120),
            'start_date' => valid_date(input('start_date')),
            'end_date' => valid_date(input('end_date')),
            'status' => in_list(input('status'), ['planifie', 'en_cours', 'termine'], 'planifie'),
        ];
        if ($data['title'] === '' || $data['scope'] === '' || $data['auditor'] === '') $errors[] = 'Intitulé, périmètre et auditeur obligatoires.';
        if ($data['start_date'] === null) $errors[] = 'Date de début invalide.';
        if (!$errors) {
            if ($audit) {
                q('UPDATE audits SET title=?, type=?, scope=?, auditor=?, start_date=?, end_date=?, status=? WHERE id=?', [...array_values($data), $id]);
                log_action('modification', 'audit', $id, $data['title']);
            } else {
                q('INSERT INTO audits (title, type, scope, auditor, start_date, end_date, status, created_at) VALUES (?,?,?,?,?,?,?,?)', [...array_values($data), now()]);
                $id = (int) db()->lastInsertId();
                log_action('creation', 'audit', $id, $data['title']);
            }
            flash('Audit enregistré.');
            redirect('audit_view', ['id' => $id]);
        }
        $audit = $data + ['id' => $id];
    }
    render('audit_form', ['audit' => $audit, 'errors' => $errors]);
}

function page_audit_view(): void
{
    require_perm('read');
    $audit = one('SELECT * FROM audits WHERE id = ?', [(int) ($_GET['id'] ?? 0)]);
    if (!$audit) redirect('audits');
    $findings = all('SELECT f.*, c.ref AS control_ref FROM findings f LEFT JOIN controls c ON c.id = f.control_id WHERE f.audit_id = ? ORDER BY f.id', [$audit['id']]);
    render('audit_view', ['audit' => $audit, 'findings' => $findings]);
}

function page_finding_edit(): void
{
    require_perm('audit_write');
    $id = (int) ($_GET['id'] ?? 0);
    $f = $id ? one('SELECT * FROM findings WHERE id = ?', [$id]) : null;
    $auditId = (int) ($f['audit_id'] ?? ($_GET['audit'] ?? 0));
    $audit = one('SELECT * FROM audits WHERE id = ?', [$auditId]);
    if (!$audit) redirect('audits');
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cid = (int) input('control_id', 0);
        $data = [
            'title' => mb_substr((string) input('title'), 0, 200),
            'severity' => in_list(input('severity'), array_keys(LABELS['severity']), 'moyenne'),
            'control_id' => ($cid && one('SELECT id FROM controls WHERE id = ?', [$cid])) ? $cid : null,
            'recommendation' => mb_substr((string) input('recommendation'), 0, 600),
            'action_owner' => mb_substr((string) input('action_owner'), 0, 120),
            'due_date' => valid_date(input('due_date')),
            'status' => in_list(input('status'), ['ouvert', 'en_cours', 'corrige', 'accepte'], 'ouvert'),
        ];
        if ($data['title'] === '' || $data['recommendation'] === '' || $data['action_owner'] === '') $errors[] = 'Constat, recommandation et responsable obligatoires.';
        if (!$errors) {
            if ($f) {
                q('UPDATE findings SET title=?, severity=?, control_id=?, recommendation=?, action_owner=?, due_date=?, status=? WHERE id=?', [...array_values($data), $id]);
                log_action('modification', 'finding', $id, $data['title'] . ' → ' . $data['status']);
            } else {
                q('INSERT INTO findings (title, severity, control_id, recommendation, action_owner, due_date, status, audit_id, created_at) VALUES (?,?,?,?,?,?,?,?,?)', [...array_values($data), $auditId, now()]);
                log_action('creation', 'finding', (int) db()->lastInsertId(), $data['title']);
            }
            flash('Constat enregistré.');
            redirect('audit_view', ['id' => $auditId]);
        }
        $f = $data + ['id' => $id];
    }
    render('finding_form', ['f' => $f, 'audit' => $audit, 'errors' => $errors, 'controls' => all('SELECT id, ref, title FROM controls ORDER BY id')]);
}

/* ---------- Rapport Direction générale ---------- */

function page_report(): void
{
    require_perm('read');
    log_action('consultation', 'rapport_dg');
    $topRisks = all("SELECT r.*, a.name AS asset_name FROM risks r LEFT JOIN assets a ON a.id = r.asset_id WHERE r.status <> 'clos' ORDER BY (r.likelihood*r.impact) DESC LIMIT 10");
    render('report', [
        'topRisks' => $topRisks,
        'matrix' => risk_matrix(false),
        'matrixRes' => risk_matrix(true),
        'applicable' => (int) scalar('SELECT COUNT(*) FROM controls WHERE applicable = 1'),
        'implemented' => (int) scalar("SELECT COUNT(*) FROM controls WHERE applicable = 1 AND status = 'implemente'"),
        'inProgress' => (int) scalar("SELECT COUNT(*) FROM controls WHERE applicable = 1 AND status = 'en_cours'"),
        'reqStats' => all('SELECT compliance, COUNT(*) AS n FROM requirements GROUP BY compliance'),
        'findings' => all("SELECT severity, COUNT(*) AS n FROM findings WHERE status IN ('ouvert','en_cours') GROUP BY severity"),
        'overdue' => all("SELECT f.*, a.title AS audit_title FROM findings f JOIN audits a ON a.id=f.audit_id WHERE f.status IN ('ouvert','en_cours') AND f.due_date < ? ORDER BY f.due_date", [date('Y-m-d')]),
    ]);
}

/* ---------- Administration ---------- */

function page_users(): void
{
    require_perm('admin');
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = input('action');
        if ($action === 'create') {
            $email = mb_strtolower((string) input('email'));
            $pwd = (string) ($_POST['password'] ?? '');
            $role = in_list(input('role'), array_keys(PERMISSIONS), 'lecteur');
            $name = mb_substr((string) input('full_name'), 0, 120);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'E-mail invalide.';
            if ($name === '') $errors[] = 'Nom obligatoire.';
            if ($pe = password_policy_errors($pwd)) $errors[] = 'Mot de passe : ' . implode(', ', $pe) . '.';
            if (one('SELECT id FROM users WHERE email = ?', [$email])) $errors[] = 'Cet e-mail existe déjà.';
            if (!$errors) {
                q('INSERT INTO users (full_name, email, password_hash, role, is_active, created_at) VALUES (?,?,?,?,1,?)', [$name, $email, password_hash($pwd, PASSWORD_DEFAULT), $role, now()]);
                log_action('creation', 'user', (int) db()->lastInsertId(), $email . ' (' . $role . ')');
                flash('Utilisateur créé.');
                redirect('users');
            }
        } elseif ($action === 'toggle') {
            $uid = (int) input('id');
            if ($uid === (int) current_user()['id']) {
                $errors[] = 'Vous ne pouvez pas désactiver votre propre compte.';
            } else {
                q('UPDATE users SET is_active = 1 - is_active WHERE id = ?', [$uid]);
                log_action('activation_basculee', 'user', $uid);
                flash('Statut du compte modifié.');
                redirect('users');
            }
        }
    }
    render('users', ['users' => all('SELECT * FROM users ORDER BY role, full_name'), 'errors' => $errors]);
}

function page_journal(): void
{
    require_perm('admin');
    $rows = all('SELECT l.*, u.email FROM audit_log l LEFT JOIN users u ON u.id = l.user_id ORDER BY l.id DESC LIMIT 300');
    render('journal', ['rows' => $rows]);
}

/** Suppression centralisée (POST + CSRF + permission + journalisation). */
function page_delete(): void
{
    $map = [
        'asset' => ['assets', 'write', 'assets'],
        'risk' => ['risks', 'write', 'risks'],
        'requirement' => ['requirements', 'write', 'requirements'],
        'audit' => ['audits', 'audit_write', 'audits'],
        'finding' => ['findings', 'audit_write', 'audits'],
    ];
    $type = (string) ($_POST['type'] ?? '');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($map[$type])) redirect('dashboard');
    [$table, $perm, $back] = $map[$type];
    require_perm($perm);
    $id = (int) ($_POST['id'] ?? 0);
    db()->beginTransaction();
    match ($type) {
        'asset' => q('UPDATE risks SET asset_id = NULL WHERE asset_id = ?', [$id]),
        'risk' => q('DELETE FROM risk_controls WHERE risk_id = ?', [$id]),
        'requirement' => q('DELETE FROM requirement_controls WHERE requirement_id = ?', [$id]),
        'audit' => q('DELETE FROM findings WHERE audit_id = ?', [$id]),
        default => null,
    };
    q("DELETE FROM $table WHERE id = ?", [$id]); // $table provient d'une liste blanche
    db()->commit();
    log_action('suppression', $type, $id);
    flash('Élément supprimé.');
    redirect($back);
}
