<?php

namespace Database\Seeders;

use App\Models\Departement;
use App\Models\Direction;
use App\Models\Service;
use Illuminate\Database\Seeder;

class StructureOrganisationnelleSeeder extends Seeder
{
    public function run(): void
    {
        $departements = [
            [
                'code' => 'SG',
                'libelle' => 'Secrétariat Général',
                'libelle_court' => 'SG',
                'type' => 'appui',
                'description' => 'Coordination administrative, fonctions de soutien et modernisation interne de la Commission.',
                'ordre_affichage' => 0,
                'directions' => [
                    ['code' => 'DAF', 'libelle' => 'Direction des Affaires Financières', 'libelle_court' => 'Affaires Financières', 'type_direction' => 'appui', 'description' => 'Programmation budgétaire, exécution financière et suivi comptable.', 'services' => [['code' => 'SBF', 'libelle' => 'Service Budget et Finances', 'libelle_court' => 'Budget'], ['code' => 'SCC', 'libelle' => 'Service Comptabilité et Contrôle', 'libelle_court' => 'Comptabilité'], ['code' => 'SMF', 'libelle' => 'Service Marchés et Fournisseurs', 'libelle_court' => 'Marchés']]],
                    ['code' => 'DRH', 'libelle' => 'Direction des Ressources Humaines', 'libelle_court' => 'Ressources Humaines', 'type_direction' => 'appui', 'description' => 'Gestion des effectifs, développement des compétences et performance interne.', 'services' => [['code' => 'SGC', 'libelle' => 'Service Gestion des Carrières', 'libelle_court' => 'Carrières'], ['code' => 'SFP', 'libelle' => 'Service Formation et Performance', 'libelle_court' => 'Formation'], ['code' => 'SPA', 'libelle' => 'Service Paie et Administration', 'libelle_court' => 'Paie']]],
                    ['code' => 'DAJ', 'libelle' => 'Direction des Affaires Juridiques', 'libelle_court' => 'Affaires Juridiques', 'type_direction' => 'appui', 'description' => 'Sécurisation juridique des actes communautaires et suivi contentieux.', 'services' => [['code' => 'SCL', 'libelle' => 'Service Contentieux et Légalité', 'libelle_court' => 'Contentieux'], ['code' => 'SAS', 'libelle' => 'Service Appui Statutaire', 'libelle_court' => 'Statutaire']]],
                    ['code' => 'DCP', 'libelle' => 'Direction de la Communication et du Protocole', 'libelle_court' => 'Communication', 'type_direction' => 'appui', 'description' => 'Communication institutionnelle, visibilité et gestion protocolaire.', 'services' => [['code' => 'SCR', 'libelle' => 'Service Communication et Relations médias', 'libelle_court' => 'Communication'], ['code' => 'SPT', 'libelle' => 'Service Protocole et Traduction', 'libelle_court' => 'Protocole']]],
                    ['code' => 'DIN', 'libelle' => 'Direction Informatique et Numérique', 'libelle_court' => 'Numérique', 'type_direction' => 'appui', 'description' => 'Transformation numérique, interopérabilité et support des systèmes.', 'services' => [['code' => 'SSI', 'libelle' => 'Service Systèmes et Infrastructure', 'libelle_court' => 'Infrastructure'], ['code' => 'SDS', 'libelle' => 'Service Développement et Support applicatif', 'libelle_court' => 'Support applicatif'], ['code' => 'SGD', 'libelle' => 'Service Gouvernance des Données', 'libelle_court' => 'Données']]],
                    ['code' => 'DAG', 'libelle' => 'Direction des Affaires Générales', 'libelle_court' => 'Affaires Générales', 'type_direction' => 'appui', 'description' => 'Patrimoine, logistique, missions et services généraux.', 'services' => [['code' => 'SLM', 'libelle' => 'Service Logistique et Missions', 'libelle_court' => 'Logistique'], ['code' => 'SPA2', 'libelle' => 'Service Patrimoine et Approvisionnements', 'libelle_court' => 'Patrimoine']]],
                ],
            ],
            [
                'code' => 'DPS',
                'libelle' => 'Département Paix et Sécurité',
                'libelle_court' => 'Paix & Sécurité',
                'type' => 'technique',
                'description' => 'Prévention des conflits, sécurité collective et appui aux mécanismes régionaux.',
                'ordre_affichage' => 1,
                'directions' => [
                    ['code' => 'DCPA', 'libelle' => 'Direction Conflits et Prévention', 'libelle_court' => 'Conflits & Prévention', 'type_direction' => 'technique', 'description' => 'Alerte précoce, médiation et diplomatie préventive.', 'services' => [['code' => 'SMA', 'libelle' => 'Service Médiation et Alerte précoce', 'libelle_court' => 'Médiation'], ['code' => 'SRS', 'libelle' => 'Service Réformes sécuritaires', 'libelle_court' => 'Réformes']]],
                    ['code' => 'DOSS', 'libelle' => 'Direction des Opérations de Soutien à la Paix', 'libelle_court' => 'Opérations de Paix', 'type_direction' => 'technique', 'description' => 'Préparation opérationnelle et coordination des dispositifs régionaux.', 'services' => [['code' => 'SOP', 'libelle' => 'Service Opérations et Planification', 'libelle_court' => 'Opérations'], ['code' => 'SLS', 'libelle' => 'Service Logistique sécuritaire', 'libelle_court' => 'Logistique sécuritaire']]],
                ],
            ],
            [
                'code' => 'DIE',
                'libelle' => 'Département Intégration Économique et Commerce',
                'libelle_court' => 'Intégration Économique',
                'type' => 'technique',
                'description' => 'Facilitation du commerce, statistiques régionales et compétitivité des marchés.',
                'ordre_affichage' => 2,
                'directions' => [
                    ['code' => 'DCE', 'libelle' => 'Direction Commerce et Échanges', 'libelle_court' => 'Commerce', 'type_direction' => 'technique', 'description' => 'Libre circulation, facilitation des échanges et intégration commerciale.', 'services' => [['code' => 'SFC', 'libelle' => 'Service Facilitation du Commerce', 'libelle_court' => 'Facilitation'], ['code' => 'SNE', 'libelle' => 'Service Normalisation et Échanges', 'libelle_court' => 'Normalisation']]],
                    ['code' => 'DIM', 'libelle' => 'Direction des Investissements et Marchés', 'libelle_court' => 'Investissements', 'type_direction' => 'technique', 'description' => 'Climat des affaires, investissement régional et attractivité.', 'services' => [['code' => 'SPI', 'libelle' => 'Service Promotion des Investissements', 'libelle_court' => 'Promotion'], ['code' => 'SOM', 'libelle' => 'Service Observation des Marchés', 'libelle_court' => 'Marchés']]],
                ],
            ],
            [
                'code' => 'DID',
                'libelle' => 'Département Infrastructure et Développement Durable',
                'libelle_court' => 'Infrastructure',
                'type' => 'technique',
                'description' => 'Transport, énergie, climat et interconnexion des espaces communautaires.',
                'ordre_affichage' => 3,
                'directions' => [
                    ['code' => 'DTI', 'libelle' => 'Direction Transport et Infrastructure', 'libelle_court' => 'Transport', 'type_direction' => 'technique', 'description' => 'Corridors, mobilité régionale et infrastructures structurantes.', 'services' => [['code' => 'SCI', 'libelle' => 'Service Corridors et Interconnexion', 'libelle_court' => 'Corridors'], ['code' => 'SPM', 'libelle' => 'Service Planification des Mobilités', 'libelle_court' => 'Mobilité']]],
                    ['code' => 'DEN', 'libelle' => 'Direction Énergie et Environnement', 'libelle_court' => 'Énergie & Environnement', 'type_direction' => 'technique', 'description' => 'Énergie régionale, résilience climatique et bassin du Congo.', 'services' => [['code' => 'SEP', 'libelle' => 'Service Énergie et Projets', 'libelle_court' => 'Énergie'], ['code' => 'SRC', 'libelle' => 'Service Résilience climatique', 'libelle_court' => 'Climat']]],
                ],
            ],
            [
                'code' => 'DDH',
                'libelle' => 'Département Développement Humain et Genre',
                'libelle_court' => 'Développement Humain',
                'type' => 'technique',
                'description' => 'Capital humain, santé, éducation, emploi et inclusion.',
                'ordre_affichage' => 4,
                'directions' => [
                    ['code' => 'DDS', 'libelle' => 'Direction Développement Social', 'libelle_court' => 'Développement Social', 'type_direction' => 'technique', 'description' => 'Programmes sociaux, santé régionale et capacités humaines.', 'services' => [['code' => 'SSA', 'libelle' => 'Service Santé et Actions sociales', 'libelle_court' => 'Santé'], ['code' => 'SJC', 'libelle' => 'Service Jeunesse et Compétences', 'libelle_court' => 'Jeunesse']]],
                    ['code' => 'DGF', 'libelle' => 'Direction Genre et Famille', 'libelle_court' => 'Genre', 'type_direction' => 'technique', 'description' => 'Égalité de genre, autonomisation économique et filets sociaux.', 'services' => [['code' => 'SAF', 'libelle' => 'Service Autonomisation des Femmes', 'libelle_court' => 'Autonomisation'], ['code' => 'SPI2', 'libelle' => 'Service Protection et Inclusion', 'libelle_court' => 'Inclusion']]],
                ],
            ],
        ];

        foreach ($departements as $departementData) {
            $directions = $departementData['directions'];
            unset($departementData['directions']);

            $departement = Departement::updateOrCreate(['code' => $departementData['code']], $departementData);

            foreach ($directions as $directionData) {
                $services = $directionData['services'];
                unset($directionData['services']);

                $direction = Direction::updateOrCreate(
                    ['code' => $directionData['code']],
                    $directionData + ['departement_id' => $departement->id]
                );

                foreach ($services as $index => $serviceData) {
                    Service::updateOrCreate(
                        ['code' => $serviceData['code']],
                        $serviceData + [
                            'direction_id' => $direction->id,
                            'description' => "Service opérationnel de {$direction->libelle}.",
                            'ordre_affichage' => $index + 1,
                            'actif' => true,
                        ]
                    );
                }
            }
        }

        $this->command->info('Structure organisationnelle enrichie avec succès.');
    }
}
