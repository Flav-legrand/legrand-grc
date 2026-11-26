-- Legrand GRC — schéma de base de données
-- Jetons remplacés par database/install.php selon le pilote :
--   {PK}     -> clé primaire auto-incrémentée
--   {ENGINE} -> options de table MySQL (vide en SQLite)

CREATE TABLE users (
  id {PK},
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'lecteur', -- admin | rssi | auditeur | lecteur
  is_active INTEGER NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  last_login_at DATETIME NULL
) {ENGINE};

CREATE TABLE login_attempts (
  id {PK},
  email VARCHAR(160) NOT NULL,
  ip VARCHAR(45) NOT NULL,
  success INTEGER NOT NULL DEFAULT 0,
  attempted_at DATETIME NOT NULL
) {ENGINE};

CREATE TABLE audit_log (
  id {PK},
  user_id INTEGER NULL,
  action VARCHAR(40) NOT NULL,
  entity VARCHAR(40) NOT NULL,
  entity_id INTEGER NULL,
  details VARCHAR(500) NULL,
  ip VARCHAR(45) NULL,
  created_at DATETIME NOT NULL
) {ENGINE};

-- Actifs (ISO 27005 : actifs primordiaux et actifs supports)
CREATE TABLE assets (
  id {PK},
  name VARCHAR(150) NOT NULL,
  category VARCHAR(40) NOT NULL,      -- Processus métier, Information, Application, Serveur, Réseau, Personne, Site, Fournisseur
  owner VARCHAR(120) NOT NULL,
  description TEXT NULL,
  confidentiality INTEGER NOT NULL DEFAULT 2, -- 1 à 4
  integrity INTEGER NOT NULL DEFAULT 2,
  availability INTEGER NOT NULL DEFAULT 2,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) {ENGINE};

-- Mesures ISO/IEC 27002:2022 et déclaration d'applicabilité (SoA)
CREATE TABLE controls (
  id {PK},
  ref VARCHAR(10) NOT NULL UNIQUE,    -- ex. 5.1, 8.24
  theme VARCHAR(20) NOT NULL,         -- Organisationnelle, Humaine, Physique, Technologique
  title VARCHAR(200) NOT NULL,
  applicable INTEGER NOT NULL DEFAULT 1,
  justification VARCHAR(500) NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'non_commence', -- non_commence | en_cours | implemente | non_applicable
  owner VARCHAR(120) NULL,
  evidence VARCHAR(500) NULL,
  updated_at DATETIME NULL
) {ENGINE};

-- Exigences du RGSSI (ANSSI Congo) — à alimenter depuis le texte officiel
CREATE TABLE requirements (
  id {PK},
  framework VARCHAR(30) NOT NULL DEFAULT 'RGSSI',
  ref VARCHAR(30) NOT NULL,
  domain VARCHAR(80) NOT NULL,
  title VARCHAR(300) NOT NULL,
  compliance VARCHAR(20) NOT NULL DEFAULT 'a_evaluer', -- a_evaluer | conforme | partiel | non_conforme | na
  comment VARCHAR(500) NULL,
  updated_at DATETIME NULL
) {ENGINE};

CREATE TABLE requirement_controls (
  requirement_id INTEGER NOT NULL,
  control_id INTEGER NOT NULL,
  PRIMARY KEY (requirement_id, control_id)
) {ENGINE};

-- Registre des risques (ISO 27005 / EBIOS RM simplifié)
CREATE TABLE risks (
  id {PK},
  ref VARCHAR(20) NOT NULL UNIQUE,
  title VARCHAR(200) NOT NULL,
  asset_id INTEGER NULL,
  threat VARCHAR(300) NOT NULL,
  vulnerability VARCHAR(300) NOT NULL,
  consequence VARCHAR(300) NULL,
  likelihood INTEGER NOT NULL,        -- 1 à 4
  impact INTEGER NOT NULL,            -- 1 à 4
  treatment VARCHAR(20) NOT NULL DEFAULT 'reduire', -- reduire | accepter | transferer | eviter
  residual_likelihood INTEGER NULL,
  residual_impact INTEGER NULL,
  owner VARCHAR(120) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'ouvert', -- ouvert | en_traitement | clos
  due_date DATE NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) {ENGINE};

CREATE TABLE risk_controls (
  risk_id INTEGER NOT NULL,
  control_id INTEGER NOT NULL,
  PRIMARY KEY (risk_id, control_id)
) {ENGINE};

-- Audits de sécurité, constats et plans d'action
CREATE TABLE audits (
  id {PK},
  title VARCHAR(200) NOT NULL,
  type VARCHAR(30) NOT NULL,          -- Interne, Externe (PASSI), Test d'intrusion, Contrôle ANSSI
  scope VARCHAR(500) NOT NULL,
  auditor VARCHAR(120) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'planifie', -- planifie | en_cours | termine
  created_at DATETIME NOT NULL
) {ENGINE};

CREATE TABLE findings (
  id {PK},
  audit_id INTEGER NOT NULL,
  title VARCHAR(200) NOT NULL,
  severity VARCHAR(20) NOT NULL,      -- faible | moyenne | elevee | critique
  control_id INTEGER NULL,
  recommendation VARCHAR(600) NOT NULL,
  action_owner VARCHAR(120) NOT NULL,
  due_date DATE NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'ouvert', -- ouvert | en_cours | corrige | accepte
  created_at DATETIME NOT NULL
) {ENGINE};

CREATE INDEX idx_risks_asset ON risks(asset_id);
CREATE INDEX idx_findings_audit ON findings(audit_id);
CREATE INDEX idx_login_email ON login_attempts(email, attempted_at);
