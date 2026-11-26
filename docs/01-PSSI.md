# Politique de Sécurité des Systèmes d'Information (PSSI)

**Organisation :** Banque Mayombe (organisation fictive de démonstration) · **Version :** 1.0 · **Classification :** Interne
**Rédacteur :** RSSI · **Approbation :** Direction générale · **Revue :** annuelle ou après tout incident majeur

> Modèle rédigé pour le projet Legrand GRC. Les références renvoient à l'ISO/IEC 27001:2022, à l'ISO/IEC 27002:2022 et au RGSSI de l'ANSSI Congo (à aligner article par article sur le texte officiel).

---

## 1. Objet et engagement de la Direction

La présente politique définit les principes que l'organisation applique pour protéger la **confidentialité**, l'**intégrité** et la **disponibilité** de ses informations et de ses systèmes. Elle est approuvée par la Direction générale, qui s'engage à :

- fournir les ressources nécessaires au système de management de la sécurité de l'information (SMSI) ;
- fixer les critères d'acceptation des risques et accepter formellement les risques résiduels ;
- revoir les performances du SMSI au moins une fois par an (revue de direction) ;
- se conformer aux exigences légales et réglementaires applicables, notamment le **RGSSI** et les contrôles de l'**ANSSI Congo**.

*Références : ISO 27001 §5.1 et §5.2 ; ISO 27002 5.1, 5.4.*

## 2. Périmètre

Tous les systèmes d'information du siège (Brazzaville) et des agences : core banking, banque en ligne, passerelle Mobile Money, infrastructure, postes de travail, données clients. La politique s'applique aux employés, aux stagiaires et aux prestataires.

## 3. Organisation de la sécurité (ISO 27002 5.2, 5.3)

| Rôle | Responsabilités principales |
|---|---|
| Direction générale | Approuve la PSSI, arbitre les ressources, accepte les risques résiduels |
| RSSI | Pilote le SMSI, l'analyse des risques, la conformité RGSSI, les audits, la gestion des incidents ; reporting mensuel à la DG |
| Comité sécurité (trimestriel) | DG, RSSI, DSI, Conformité, Opérations, RH : suivi des risques et des plans d'action |
| DSI et équipes techniques | Mettent en œuvre les mesures techniques |
| Propriétaires d'actifs | Classifient leurs actifs et valident les droits d'accès |
| Tous les utilisateurs | Respectent la charte informatique et signalent les événements de sécurité |

Le RSSI est **indépendant de l'exploitation** : il ne valide pas lui-même les changements qu'il audite (séparation des tâches).

## 4. Principes de sécurité

### 4.1 Gestion des risques (ISO 27001 §6.1, ISO 27005)
L'appréciation des risques est conduite au moins une fois par an et à chaque projet ou changement majeur, selon la méthode décrite dans `02-methode-analyse-risques.md`. Les résultats sont tenus dans le registre des risques de Legrand GRC.

### 4.2 Gestion et classification des actifs (5.9 à 5.13)
Chaque actif a un propriétaire et est classé selon quatre niveaux : **Public, Interne, Confidentiel, Secret**. Les données clients et les données de transactions sont au minimum **Confidentielles**.

### 4.3 Contrôle d'accès (5.15 à 5.18, 8.2 à 8.5)
- Le moindre privilège et le besoin d'en connaître s'appliquent.
- Chaque utilisateur a un compte nominatif ; les comptes partagés sont interdits.
- L'authentification multifacteur (MFA) est obligatoire pour les accès à distance, les comptes à privilèges et la messagerie.
- Les habilitations sont revues tous les six mois par les propriétaires d'actifs ; les départs sont traités sous 24 h.
- Les mots de passe font au moins 12 caractères ; ceux des comptes à privilèges sont conservés dans un coffre-fort numérique.

### 4.4 Cryptographie (8.24)
- Tous les flux externes sont chiffrés en TLS 1.2 minimum (TLS 1.3 de préférence).
- Les données sensibles sont chiffrées au repos et les clés gérées selon une procédure dédiée.

### 4.5 Sécurité de l'exploitation (8.7 à 8.9, 8.13, 8.15 à 8.17)
- Antivirus/EDR sur tous les postes et serveurs.
- Correctifs critiques appliqués sous 15 jours (7 jours pour les systèmes exposés à Internet).
- Sauvegardes quotidiennes selon la règle **3-2-1**, dont une copie hors ligne ; un test de restauration par trimestre.
- Journaux centralisés dans le SIEM, conservés au moins 12 mois, horloges synchronisées par NTP.

### 4.6 Sécurité des réseaux (8.20 à 8.23)
Le réseau est cloisonné en zones (DMZ, serveurs, utilisateurs, administration, Mobile Money). Les flux sont filtrés par défaut (tout ce qui n'est pas autorisé est interdit), et l'administration passe par un bastion.

### 4.7 Sécurité dans les projets et le développement (5.8, 8.25 à 8.29, 8.31, 8.32)
La sécurité est intégrée dès la conception (*security by design*) : exigences de sécurité dans les cahiers des charges, revue d'architecture, codage sécurisé (OWASP), tests SAST/DAST dans la chaîne CI/CD, test d'intrusion avant toute mise en production exposée, environnements séparés.

### 4.8 Relations avec les fournisseurs (5.19 à 5.23)
Les contrats comportent des clauses de sécurité : confidentialité, droit d'audit, notification des incidents sous 24 h, réversibilité. Les accès à distance des prestataires sont ponctuels, tracés et passent par le bastion.

### 4.9 Gestion des incidents (5.24 à 5.28, 6.8)
Tout événement de sécurité est signalé au RSSI. Les incidents sont qualifiés, traités et clôturés selon les playbooks du SOC (projet Legrand SOC), puis font l'objet d'un retour d'expérience. Les incidents significatifs sont déclarés aux autorités compétentes (ANSSI Congo, régulateur bancaire) dans les délais réglementaires.

### 4.10 Continuité d'activité (5.29, 5.30, 8.14)
Un PCA/PRA est maintenu et testé une fois par an (voir `03-PCA-PRA.md`).

### 4.11 Ressources humaines et sensibilisation (6.1 à 6.6)
Une clause de confidentialité figure dans chaque contrat. Les nouveaux arrivants sont sensibilisés à leur arrivée, puis tous les collaborateurs une fois par an, avec des campagnes de simulation d'hameçonnage.

### 4.12 Sécurité physique (7.1 à 7.14)
La salle serveurs est en accès contrôlé par badge et journalisé, avec vidéosurveillance, climatisation redondante, onduleurs et groupe électrogène testé tous les mois.

## 5. Conformité et contrôle (5.31, 5.35, 5.36)

- Suivi de la conformité au RGSSI dans Legrand GRC (exigence → mesures ISO 27002 → preuves).
- Audit interne annuel du SMSI ; audit par un prestataire d'audit agréé selon les exigences de l'ANSSI.
- Tableau de bord mensuel à la Direction générale.

## 6. Sanctions

Le non-respect de la présente politique peut entraîner des sanctions disciplinaires, conformément au règlement intérieur (ISO 27002 6.4).

## 7. Indicateurs de pilotage

| Indicateur | Cible |
|---|---|
| Mesures applicables implémentées | ≥ 80 % à 12 mois |
| Risques résiduels critiques | 0 non acceptés formellement |
| Correctifs critiques appliqués dans les délais | ≥ 95 % |
| Taux de clic aux campagnes d'hameçonnage | < 10 % |
| Tests de restauration réussis | 4 par an |
| Délai moyen de traitement d'un incident majeur | < 4 h pour le confinement |
