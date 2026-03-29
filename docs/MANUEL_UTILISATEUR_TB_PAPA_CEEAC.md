# Manuel utilisateur

## Application
TB-PAPA-CEEAC

Tableau de bord du Plan d'Action Prioritaire Annuel de la Commission de la CEEAC.

## Objet du manuel
Ce manuel explique l'utilisation courante de l'application pour les profils institutionnels impliques dans la planification, le suivi, la validation, le controle et l'audit des PAPAs.

## Public cible
- Super administrateur
- President
- Vice-president
- Secretaire general
- Commissaires
- Directeurs techniques et d'appui
- Chefs de service
- Points focaux
- Auditeurs internes
- Controle financier
- Agence comptable
- Administrateurs fonctionnels
- Lecteurs

## Acces a l'application

### Adresse
- Interface publique: `/`
- Espace applicatif: `/login`

### Connexion
Chaque utilisateur se connecte avec:
- son adresse e-mail professionnelle
- son mot de passe

### Comptes de demonstration seedes
- Super administrateur: `admin@ceeac-eccas.org` / `Admin@2025!`
- President: `president@ceeac-eccas.org` / `President@2025!`
- Secretaire general: `sg@ceeac-eccas.org` / `SG@2025!`
- Administratrice fonctionnelle: `admin.fonctionnel@ceeac-eccas.org` / `AdminFonctionnel@2025!`
- Auditeur interne: `audit@ceeac-eccas.org` / `Audit@2025!`

Important:
- les droits affiches dans les menus dependent du role de l'utilisateur
- un utilisateur inactif ne peut pas se connecter

## Navigation generale

### Barre laterale
La barre laterale donne acces aux grands modules:
- Tableau de bord
- Plans d'action
- Actions prioritaires
- Objectifs immediats
- Resultats attendus
- Rapports
- Activites
- Diagramme Gantt
- Indicateurs
- Budget
- Risques
- Workflows
- Decisions
- Alertes
- GED / Documents
- Administration

### Barre superieure
La barre superieure permet:
- d'ouvrir le menu mobile
- de consulter les notifications
- d'identifier l'utilisateur connecte
- de se deconnecter

## Tableau de bord

Le tableau de bord varie selon le role.

### Ce que l'utilisateur y trouve
- taux d'execution physique
- taux d'execution financiere
- evolution trimestrielle
- comparaison par departement
- repartition des activites par statut
- alertes critiques, majeures et mineures
- acces rapides vers les modules principaux

### Logique d'affichage
- President et super administrateur: vue executive globale
- Vice-president: vue executive consolidee
- Commissaire: vue sectorielle
- Secretaire general: vue de coordination
- Directeurs: vue de pilotage directionnel
- Chefs de service et points focaux: vue operationnelle
- Audit et controle financier: vue de controle

## Gestion des PAPAs

### Liste des PAPAs
Le module `Plans d'action` permet de:
- consulter les PAPAs existants
- filtrer les exercices
- ouvrir un PAPA en detail
- creer un nouveau PAPA selon les droits

### Creation d'un PAPA
Informations minimales:
- code
- libelle
- annee
- date de debut
- date de fin

Le PAPA est cree au statut `brouillon`.

### Fiche PAPA
La fiche PAPA centralise:
- les informations generales
- les actions prioritaires
- les budgets
- les risques
- les workflows lies
- les decisions liees
- l'historique de validation

### Actions possibles sur un PAPA
Selon les droits:
- modifier
- soumettre
- valider
- rejeter
- archiver
- cloner
- recalculer les taux
- ouvrir l'audit associe

### Statuts usuels
- brouillon
- soumis
- valide
- archive

## Actions prioritaires, objectifs et resultats

La chaine de planification suit cette logique:
1. PAPA
2. Action prioritaire
3. Objectif immediat
4. Resultat attendu
5. Activite

### Bonnes pratiques
- utiliser des intitules clairs et institutionnels
- verifier le rattachement au bon PAPA
- eviter les doublons
- s'assurer que chaque resultat attendu a des activites mesurables

## Activites et diagramme de Gantt

### Activites
Le module `Activites` permet de:
- creer une activite
- la rattacher a un resultat attendu
- suivre son taux de realisation
- mettre a jour son avancement
- consulter son statut

### Gantt
Le diagramme de Gantt permet de:
- visualiser les activites dans le temps
- reperer les retards
- voir les chevauchements
- mieux suivre le rythme d'execution

## Indicateurs

Le module `Indicateurs` permet de:
- definir les indicateurs de performance
- saisir des valeurs periodiques
- valider les valeurs
- suivre l'atteinte des resultats

### Donnees utiles
- libelle de l'indicateur
- frequence
- unite
- valeurs
- validation des donnees

## Budget

Le module `Budget` est accessible depuis le PAPA courant.

### Fonctions principales
- consulter les lignes budgetaires
- creer une ligne de budget
- modifier les montants
- suivre le prevu et le decaisse

### Conseils
- verifier le rattachement a l'action prioritaire
- maintenir la coherence entre budget et execution physique

## Risques

Le module `Risques` permet de:
- enregistrer les risques du PAPA
- suivre les risques ouverts
- mettre a jour les informations
- documenter les risques majeurs

## Alertes

Le module `Alertes` permet de:
- consulter les alertes generees
- traiter une alerte
- escalader une alerte
- generer des alertes pour un PAPA

### Cas frequents
- retard d'activite
- sous-performance
- probleme de validation
- ecart de pilotage

## Workflows institutionnels

Le module `Workflows` permet de suivre les circuits de validation.

### Fonctions principales
- consulter les workflows
- ouvrir un workflow
- approuver une etape
- rejeter un workflow
- ajouter un commentaire
- ouvrir l'audit associe

### Bonnes pratiques
- toujours commenter une decision sensible
- utiliser le rejet avec motif explicite
- consulter l'audit si un workflow parait incoherent

## Decisions et arbitrages

Le module `Decisions` centralise:
- les arbitrages
- les validations
- les reaffectations
- les decisions executees

### Fonctions principales
- creer une decision
- rattacher un document
- valider
- executer
- ouvrir l'audit associe

## GED / Documents

Le module GED permet de:
- deposer un document
- consulter les metadonnees
- valider un document
- archiver un document
- telecharger un fichier
- exporter l'audit documentaire

### Conseils
- nommer les documents clairement
- verifier la confidentialite
- relier le bon document au bon objet

## Rapports

Le module `Rapports` permet de:
- consulter les rapports
- creer un rapport
- valider un rapport
- publier un rapport
- exporter en PDF ou selon les options disponibles

## Notifications

Le centre de notifications permet de:
- consulter les notifications recues
- filtrer par statut
- marquer une notification comme lue
- marquer toutes les notifications comme lues

Les notifications proviennent principalement:
- des workflows
- des decisions
- des evenements critiques d'audit

## Audit metier

Le module `Audit metier` est accessible dans l'administration selon les droits.

### Fonctions disponibles
- consulter le journal d'audit
- filtrer par acteur
- filtrer par module
- filtrer par niveau
- filtrer par PAPA
- filtrer par periode
- filtrer par objet audite
- exporter en CSV
- revenir vers l'objet source

### Ce que montre l'audit
- horodatage
- module
- evenement
- acteur
- PAPA associe
- objet source
- description
- checksum

## Administration

### Utilisateurs
Le module utilisateurs permet de:
- creer un compte
- modifier un compte
- activer ou desactiver un utilisateur
- restaurer un compte
- associer un role

### Structure organisationnelle
Le module structure permet de gerer:
- departements
- directions
- services

### Regles de notification
Le module `Notifications` d'administration permet de:
- consulter les regles
- modifier les canaux
- modifier les roles cibles
- activer ou desactiver une regle

## Regles de gestion importantes
- les menus visibles dependent des permissions
- un PAPA archive est verrouille
- une validation cree une trace de workflow
- les actions sensibles sont tracees dans l'audit metier
- certaines operations ne sont autorisees qu'a des roles specifiques

## Conseils d'utilisation en demonstration
- commencer par le tableau de bord
- ouvrir un PAPA valide
- montrer une action prioritaire et ses activites
- passer par les indicateurs
- ouvrir une decision et son audit
- ouvrir un workflow et son audit
- montrer l'ecran d'audit admin
- terminer par les notifications

## Procedure rapide de demonstration
1. Se connecter avec un compte institutionnel.
2. Ouvrir le tableau de bord.
3. Consulter un PAPA.
4. Ouvrir les activites et le Gantt.
5. Consulter les indicateurs.
6. Montrer les budgets et risques.
7. Ouvrir un workflow.
8. Ouvrir une decision.
9. Consulter l'audit metier.
10. Montrer les notifications.

## Depannage

### Je ne vois pas un menu
Cause probable:
- permission absente
- role inadapté

Action:
- verifier le role utilisateur
- verifier que le compte est actif

### Je ne peux pas valider
Cause probable:
- permission manquante
- objet au mauvais statut

Action:
- verifier le statut de l'objet
- verifier les droits de validation

### Une page semble vide
Cause probable:
- aucune donnee disponible
- filtre trop restrictif

Action:
- reinitialiser les filtres
- verifier qu'un PAPA existe

## Support et maintenance
Pour la demonstration ou l'administration fonctionnelle:
- utiliser le compte administrateur fonctionnel
- reserver le super administrateur aux actions techniques ou de securite

## Version du manuel
- Application: APP-TB-PAPA-CEEAC
- Manuel genere le: 2026-03-29
