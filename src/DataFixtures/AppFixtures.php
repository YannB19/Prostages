<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Stage;
use App\Entreprise;
use App\Formation;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        /* Création d'un générateur de données à partir de la classe Faker*/
        $faker = \Faker\Factory::create('fr_FR');

        /***************************************
        *** CREATION DES TYPES DE RESSOURCES ***
        ****************************************/
        $dutInfo = new Formation();
        $dutInfo -> setNomLong("DUT Informatique");
        $dutInfo -> setNomCourt("DUT Info");

        $dutGea = new Formation();
        $dutGea -> setNomLong("DUT Gestion des Entreprises et des Administrations");
        $dutGea -> setNomCourt("DUT GEA");

        $lpProg = new Formation();
        $lpProg -> setNomLong("Licence Pro Programmation Avancée");
        $lpProg-> setNomCourt("LP Prog");

        $lpNum = new Formation();
        $lpNum -> setNomLong("Licence Pro Metiers du Numerique");
        $lpNum -> setNomCourt("LP Num");


        $tableauFormations = array($dutInfo,$dutGea,$lpProg,$lpNum);

        foreach ($tableauFormations as $formation) {
            $manager->persist($formation);
        }

        $nbEtp = 15;
        for ($i=1; $i <= $nbEtp; $i++) {
            $entreprise = new Entreprise();
            $entreprise->setActivite($faker->jobTitle());
            $entreprise->setAdresse($faker->address());
            $entreprise->setNom($faker->company());
            $entreprise->setURLsite(url());
            $manager->persist($entreprise);
        }




            $nbStagesGenere = $faker->numberBetween($min = 0, $max = 7);
            for ($numStage=0; $numStage < $nbStagesGenere; $numStage++) {
                $stage = new Stage();
                $stage -> setTitre($faker->sentence($nbWords = 6, $variableNbWords = true));
                $stage -> setDescMissions($faker->realText($maxNbChars = 200, $indexSize = 2));
                $stage -> setMailContact($faker->email());
                $stage -> addEntreprise($entreprise);

                /****** Définir et mettre à jour le type de ressource ******/
                // Sélectionner un type de ressource au hasard parmi les 8 types enregistrés dans $tableauTypesRessources
                $numFormation = $faker->numberBetween($min = 0, $max = 4);
                // Création relation Ressource --> TypeRessource
                $stage -> setFormation($tableauFormations[$numFormation]);
                // Création relation TypeRessource --> Ressource
                $tableauFormations[$numStage] -> addStage($stage);

                // Persister les objets modifiés
                $manager->persist($stage);
                $manager->persist($tableauFormations[$numFormation]);
            }
        }


        $manager->fflush();
    }
}
