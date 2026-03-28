<?php

namespace Database\Seeders;

use App\Models\Direction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UtilisateursSeeder extends Seeder
{
    public function run(): void
    {
        $directions = Direction::query()->get()->keyBy('code');

        $users = [
            ['prenom' => 'Super', 'name' => 'Administrateur', 'email' => 'admin@ceeac-eccas.org', 'password' => 'Admin@2025!', 'titre' => 'Monsieur', 'fonction' => 'Administrateur Système', 'matricule' => 'SYS-001', 'role' => 'super_admin', 'direction_code' => null],
            ['prenom' => 'Gillian Lionel', 'name' => 'NGOUABI', 'email' => 'president@ceeac-eccas.org', 'password' => 'President@2025!', 'titre' => 'Excellence', 'fonction' => 'Président de la Commission', 'matricule' => 'PRES-001', 'role' => 'president', 'direction_code' => null],
            ['prenom' => 'Jean-Claude', 'name' => 'OLEMA', 'email' => 'vpresident@ceeac-eccas.org', 'password' => 'VP@2025!', 'titre' => 'Excellence', 'fonction' => 'Vice-Président de la Commission', 'matricule' => 'VP-001', 'role' => 'vice_president', 'direction_code' => null],
            ['prenom' => 'Antoine', 'name' => 'MBEMBA', 'email' => 'sg@ceeac-eccas.org', 'password' => 'SG@2025!', 'titre' => 'Monsieur', 'fonction' => 'Secrétaire Général', 'matricule' => 'SG-001', 'role' => 'secretaire_general', 'direction_code' => null],
            ['prenom' => 'Julienne', 'name' => 'EYEGHE', 'email' => 'commissaire.paix@ceeac-eccas.org', 'password' => 'CommPaix@2025!', 'titre' => 'Madame', 'fonction' => 'Commissaire Paix et Sécurité', 'matricule' => 'COM-PS-001', 'role' => 'commissaire', 'direction_code' => 'DCPA'],
            ['prenom' => 'Blaise', 'name' => 'MAKITA', 'email' => 'commissaire.integration@ceeac-eccas.org', 'password' => 'CommEco@2025!', 'titre' => 'Monsieur', 'fonction' => 'Commissaire Intégration Économique', 'matricule' => 'COM-IE-001', 'role' => 'commissaire', 'direction_code' => 'DCE'],
            ['prenom' => 'Nadia', 'name' => 'OBIANG', 'email' => 'commissaire.infrastructure@ceeac-eccas.org', 'password' => 'CommInfra@2025!', 'titre' => 'Madame', 'fonction' => 'Commissaire Infrastructure et Développement Durable', 'matricule' => 'COM-INF-001', 'role' => 'commissaire', 'direction_code' => 'DTI'],
            ['prenom' => 'Sandrine', 'name' => 'MALONGA', 'email' => 'commissaire.humain@ceeac-eccas.org', 'password' => 'CommHumain@2025!', 'titre' => 'Madame', 'fonction' => 'Commissaire Développement Humain et Genre', 'matricule' => 'COM-DH-001', 'role' => 'commissaire', 'direction_code' => 'DDS'],
            ['prenom' => 'Roger', 'name' => 'MBOUMBA', 'email' => 'audit@ceeac-eccas.org', 'password' => 'Audit@2025!', 'titre' => 'Monsieur', 'fonction' => 'Auditeur Interne Principal', 'matricule' => 'AUD-001', 'role' => 'auditeur_interne', 'direction_code' => null],
            ['prenom' => 'Odette', 'name' => 'MEKA', 'email' => 'controle.financier@ceeac-eccas.org', 'password' => 'Controle@2025!', 'titre' => 'Madame', 'fonction' => 'Contrôleur Financier Central', 'matricule' => 'CF-001', 'role' => 'controle_financier', 'direction_code' => 'DAF'],
            ['prenom' => 'Franck', 'name' => 'BIKOLO', 'email' => 'agence.comptable@ceeac-eccas.org', 'password' => 'Agence@2025!', 'titre' => 'Monsieur', 'fonction' => 'Agent Comptable Principal', 'matricule' => 'AC-001', 'role' => 'agence_comptable', 'direction_code' => 'DAF'],
            ['prenom' => 'Clarisse', 'name' => 'NZAMBA', 'email' => 'admin.fonctionnel@ceeac-eccas.org', 'password' => 'AdminFonctionnel@2025!', 'titre' => 'Madame', 'fonction' => 'Administratrice Fonctionnelle PAPA', 'matricule' => 'AF-001', 'role' => 'administrateur_fonctionnel', 'direction_code' => 'DIN'],
            ['prenom' => 'Mariama', 'name' => 'DIALLO', 'email' => 'dir.daf@ceeac-eccas.org', 'password' => 'Direction@2025!', 'titre' => 'Madame', 'fonction' => 'Directrice des Affaires Financières', 'matricule' => 'DIR-001', 'role' => 'directeur_appui', 'direction_code' => 'DAF'],
            ['prenom' => 'Arlette', 'name' => 'MBA', 'email' => 'dir.drh@ceeac-eccas.org', 'password' => 'Direction@2025!', 'titre' => 'Madame', 'fonction' => 'Directrice des Ressources Humaines', 'matricule' => 'DIR-002', 'role' => 'directeur_appui', 'direction_code' => 'DRH'],
            ['prenom' => 'Richard', 'name' => 'MABIALA', 'email' => 'dir.daj@ceeac-eccas.org', 'password' => 'Direction@2025!', 'titre' => 'Monsieur', 'fonction' => 'Directeur des Affaires Juridiques', 'matricule' => 'DIR-003', 'role' => 'directeur_appui', 'direction_code' => 'DAJ'],
            ['prenom' => 'Eugénie', 'name' => 'MOUSSAVOU', 'email' => 'dir.dcp@ceeac-eccas.org', 'password' => 'Direction@2025!', 'titre' => 'Madame', 'fonction' => 'Directrice de la Communication et du Protocole', 'matricule' => 'DIR-004', 'role' => 'directeur_appui', 'direction_code' => 'DCP'],
            ['prenom' => 'Salomon', 'name' => 'EKOMIE', 'email' => 'dir.din@ceeac-eccas.org', 'password' => 'Direction@2025!', 'titre' => 'Monsieur', 'fonction' => 'Directeur Informatique et Numérique', 'matricule' => 'DIR-005', 'role' => 'directeur_appui', 'direction_code' => 'DIN'],
            ['prenom' => 'Paulin', 'name' => 'MONGO', 'email' => 'dir.dag@ceeac-eccas.org', 'password' => 'Direction@2025!', 'titre' => 'Monsieur', 'fonction' => 'Directeur des Affaires Générales', 'matricule' => 'DIR-006', 'role' => 'directeur_appui', 'direction_code' => 'DAG'],
            ['prenom' => 'François', 'name' => 'NGUEMA', 'email' => 'dir.dcpa@ceeac-eccas.org', 'password' => 'Direction@2025!', 'titre' => 'Monsieur', 'fonction' => 'Directeur Conflits et Prévention', 'matricule' => 'DIR-007', 'role' => 'directeur_technique', 'direction_code' => 'DCPA'],
            ['prenom' => 'Issa', 'name' => 'KOUYATE', 'email' => 'dir.doss@ceeac-eccas.org', 'password' => 'Direction@2025!', 'titre' => 'Monsieur', 'fonction' => 'Directeur des Opérations de Soutien à la Paix', 'matricule' => 'DIR-008', 'role' => 'directeur_technique', 'direction_code' => 'DOSS'],
            ['prenom' => 'Henriette', 'name' => 'ABENA', 'email' => 'dir.dce@ceeac-eccas.org', 'password' => 'Direction@2025!', 'titre' => 'Madame', 'fonction' => 'Directrice Commerce et Échanges', 'matricule' => 'DIR-009', 'role' => 'directeur_technique', 'direction_code' => 'DCE'],
            ['prenom' => 'Pacôme', 'name' => 'BESSA', 'email' => 'dir.dim@ceeac-eccas.org', 'password' => 'Direction@2025!', 'titre' => 'Monsieur', 'fonction' => 'Directeur des Investissements et Marchés', 'matricule' => 'DIR-010', 'role' => 'directeur_technique', 'direction_code' => 'DIM'],
            ['prenom' => 'Cédric', 'name' => 'MAMPOUYA', 'email' => 'dir.dti@ceeac-eccas.org', 'password' => 'Direction@2025!', 'titre' => 'Monsieur', 'fonction' => 'Directeur Transport et Infrastructure', 'matricule' => 'DIR-011', 'role' => 'directeur_technique', 'direction_code' => 'DTI'],
            ['prenom' => 'Amina', 'name' => 'MAHAMAT', 'email' => 'dir.den@ceeac-eccas.org', 'password' => 'Direction@2025!', 'titre' => 'Madame', 'fonction' => 'Directrice Énergie et Environnement', 'matricule' => 'DIR-012', 'role' => 'directeur_technique', 'direction_code' => 'DEN'],
            ['prenom' => 'Brigitte', 'name' => 'NZE', 'email' => 'dir.dds@ceeac-eccas.org', 'password' => 'Direction@2025!', 'titre' => 'Madame', 'fonction' => 'Directrice Développement Social', 'matricule' => 'DIR-013', 'role' => 'directeur_technique', 'direction_code' => 'DDS'],
            ['prenom' => 'Noëlla', 'name' => 'MAVOUNGOU', 'email' => 'dir.dgf@ceeac-eccas.org', 'password' => 'Direction@2025!', 'titre' => 'Madame', 'fonction' => 'Directrice Genre et Famille', 'matricule' => 'DIR-014', 'role' => 'directeur_technique', 'direction_code' => 'DGF'],
            ['prenom' => 'Hortense', 'name' => 'MBADINGA', 'email' => 'chef.sbf@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Madame', 'fonction' => 'Chef du Service Budget et Finances', 'matricule' => 'USR-001', 'role' => 'chef_service', 'direction_code' => 'DAF'],
            ['prenom' => 'Moïse', 'name' => 'IKA', 'email' => 'chef.scc@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Monsieur', 'fonction' => 'Chef du Service Comptabilité et Contrôle', 'matricule' => 'USR-002', 'role' => 'chef_service', 'direction_code' => 'DAF'],
            ['prenom' => 'Lydie', 'name' => 'BONGO', 'email' => 'chef.sgc@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Madame', 'fonction' => 'Chef du Service Gestion des Carrières', 'matricule' => 'USR-003', 'role' => 'chef_service', 'direction_code' => 'DRH'],
            ['prenom' => 'Christelle', 'name' => 'AMVO', 'email' => 'pf.dce@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Madame', 'fonction' => 'Point Focal PAPA - Commerce', 'matricule' => 'USR-004', 'role' => 'point_focal', 'direction_code' => 'DCE'],
            ['prenom' => 'Nicaise', 'name' => 'BIDIMA', 'email' => 'pf.dcpa@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Monsieur', 'fonction' => 'Point Focal PAPA - Prévention des conflits', 'matricule' => 'USR-005', 'role' => 'point_focal', 'direction_code' => 'DCPA'],
            ['prenom' => 'Mireille', 'name' => 'SIMA', 'email' => 'pf.dti@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Madame', 'fonction' => 'Point Focal PAPA - Infrastructures', 'matricule' => 'USR-006', 'role' => 'point_focal', 'direction_code' => 'DTI'],
            ['prenom' => 'Suzanne', 'name' => 'NTOUTOUME', 'email' => 'pf.den@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Madame', 'fonction' => 'Point Focal PAPA - Climat et Énergie', 'matricule' => 'USR-007', 'role' => 'point_focal', 'direction_code' => 'DEN'],
            ['prenom' => 'Armand', 'name' => 'MABIKA', 'email' => 'pf.dds@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Monsieur', 'fonction' => 'Point Focal PAPA - Développement social', 'matricule' => 'USR-008', 'role' => 'point_focal', 'direction_code' => 'DDS'],
            ['prenom' => 'Cynthia', 'name' => 'MOUNDOUNGA', 'email' => 'pf.din@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Madame', 'fonction' => 'Point Focal PAPA - Numérique', 'matricule' => 'USR-009', 'role' => 'point_focal', 'direction_code' => 'DIN'],
            ['prenom' => 'Aimé', 'name' => 'BISSIELOU', 'email' => 'chef.ssi@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Monsieur', 'fonction' => 'Chef du Service Systèmes et Infrastructure', 'matricule' => 'USR-010', 'role' => 'chef_service', 'direction_code' => 'DIN'],
            ['prenom' => 'Corinne', 'name' => 'NKOLO', 'email' => 'chef.scr@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Madame', 'fonction' => 'Chef du Service Communication et Relations médias', 'matricule' => 'USR-011', 'role' => 'chef_service', 'direction_code' => 'DCP'],
            ['prenom' => 'Jeanne', 'name' => 'MBOMBO', 'email' => 'chef.sop@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Madame', 'fonction' => 'Chef du Service Opérations et Planification', 'matricule' => 'USR-012', 'role' => 'chef_service', 'direction_code' => 'DOSS'],
            ['prenom' => 'Aubin', 'name' => 'ONDO', 'email' => 'chef.sfc@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Monsieur', 'fonction' => 'Chef du Service Facilitation du Commerce', 'matricule' => 'USR-013', 'role' => 'chef_service', 'direction_code' => 'DCE'],
            ['prenom' => 'Blandine', 'name' => 'MATSANGA', 'email' => 'chef.sep@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Madame', 'fonction' => 'Chef du Service Énergie et Projets', 'matricule' => 'USR-014', 'role' => 'chef_service', 'direction_code' => 'DEN'],
            ['prenom' => 'Yvette', 'name' => 'MADINGOU', 'email' => 'chef.saf@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Madame', 'fonction' => 'Chef du Service Autonomisation des Femmes', 'matricule' => 'USR-015', 'role' => 'chef_service', 'direction_code' => 'DGF'],
            ['prenom' => 'Patrick', 'name' => 'OBAME', 'email' => 'lecteur.presidence@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Monsieur', 'fonction' => 'Conseiller à la Présidence', 'matricule' => 'USR-016', 'role' => 'lecteur', 'direction_code' => null],
            ['prenom' => 'Cyrille', 'name' => 'MINKO', 'email' => 'juridique@ceeac-eccas.org', 'password' => 'Compte@2025!', 'titre' => 'Monsieur', 'fonction' => 'Conseiller Juridique Institutionnel', 'matricule' => 'USR-017', 'role' => 'conseiller_juridique', 'direction_code' => 'DAJ'],
        ];

        foreach ($users as $index => $data) {
            $role = $data['role'];
            $directionCode = $data['direction_code'];

            unset($data['role'], $data['direction_code']);

            $data['password'] = Hash::make($data['password']);
            $data['direction_id'] = $directionCode ? ($directions[$directionCode]->id ?? null) : null;
            $data['telephone'] = '+241 01 ' . str_pad((string) (100000 + ($index + 1) * 137), 6, '0', STR_PAD_LEFT);
            $data['locale'] = 'fr';
            $data['actif'] = true;
            $data['email_verified_at'] = now()->subDays(rand(30, 500));
            $data['derniere_connexion'] = now()->subHours(rand(3, 240));

            $user = User::updateOrCreate(['email' => $data['email']], $data);
            $user->syncRoles([$role]);
        }

        $this->command->info(sprintf('%d utilisateurs institutionnels créés ou mis à jour.', User::count()));
    }
}
