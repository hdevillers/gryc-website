<?php
// src/AppBundle/Command/AddSpeciesCommand.php

namespace AppBundle\Command;

use AppBundle\Entity\Species;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\DomCrawler\Crawler;

class AddSpeciesCommand extends ContainerAwareCommand
{
    const NCBI_TAXONOMY_API_LINK = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=taxonomy&id=";

    protected function configure()
    {
        $this
            ->setName('bio:species:add')
            ->setDescription('Add a species')
            ->addArgument(
                'ncbi',
                InputArgument::OPTIONAL,
                'NCBI taxonomy ID'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Appeller la commande bio:clade:list, pour donner à l'utilisateur une liste des clades
        $listCladeCommand = $this->getApplication()->find('bio:clade:list');
        $listCladeCommandInput = new ArrayInput(array('command' => 'bio:clade:list'));
        $listCladeCommand->run($listCladeCommandInput, $output);

        // Créer le helper
        $helper = $this->getHelper('question');

        // Récupérer les clades dans la base de données
        $em = $this->getContainer()->get('doctrine')->getManager();
        $clades = $em->getRepository('AppBundle:Clade')->findAll();

        // On récupère les noms des clades et on les met dans un array
        $cladesName = array();
        foreach ($clades as $clade) {
            $cladesName[] = $clade->getName();
        }

        do {
            // Créer l'objet Species, à remplir
            $species = new Species();

            // On demande à l'utilisateur sur quel clade il veut lier l'espèce
            $speciesCladeQuestion = new Question("Please enter the name of a clade:\n");
            $speciesCladeQuestion->setAutocompleterValues($cladesName);
            $speciesCladeResponse = $helper->ask($input, $output, $speciesCladeQuestion);
            // On récupère l'objet à partir du nom choisi
            foreach ($clades as $clade) {
                if ($speciesCladeResponse === $clade->getName()) {
                    $species->setClade($clade);
                }
            }

            // Si l'utilisateur renseigne un TaxId, on va faire une requête sur le NCBI, pour hydrater notre objet
            if ($input->getArgument('ncbi')) {
                // On récupère le contenu de la page de l'API
                $xmlString = file_get_contents(self::NCBI_TAXONOMY_API_LINK.$input->getArgument('ncbi'));

                // On crée un Crawler sur le contenu Xml
                $crawler = new Crawler($xmlString);

                // On vérifie si l'ID renseigné retourne un contenu XML valide (ici c'est un peu moche, car si c'est vide il retourne un saut de ligne...)
                if ("\n" !== $crawler->filterXPath('//TaxaSet')->text()) {
                    // Si le contenu XML retourne un rang: species
                    if ("species" === $crawler->filterXPath('//TaxaSet/Taxon/Rank')->text()) {
                        // On hydrate notre objet Species
                        $species->setTaxid($crawler->filterXPath('//TaxaSet/Taxon/TaxId')->text());
                        $species->setScientificName($crawler->filterXPath('//TaxaSet/Taxon/ScientificName')->text());
                        $scientificNameExploded = explode(' ', $crawler->filterXPath('//TaxaSet/Taxon/ScientificName')->text());
                        $species->setGenus($scientificNameExploded[0]);
                        $species->setSpecies($scientificNameExploded[1]);
                        $species->setGeneticCode($crawler->filterXPath('//TaxaSet/Taxon/GeneticCode/GCId')->text());
                        $species->setMitoCode($crawler->filterXPath('//TaxaSet/Taxon/MitoGeneticCode/MGCId')->text());
                        $species->setLineage($crawler->filterXPath('//TaxaSet/Taxon/Lineage')->text());

                        // Si il y a un DOM Synonym, alors on extrait les synonymes et les ajoutes à l'objet
                        if ($crawler->filterXPath('//TaxaSet/Taxon/OtherNames/Synonym')->text()) {
                            // On filtre sur le DOM Synonym, et on exécute un Closure qui retourne le contenu de chacun (liste des synonymes
                            $synonymes = $crawler->filterXPath('//TaxaSet/Taxon/OtherNames/Synonym')->each(function (Crawler $node, $i) {
                                return $node->text();
                            });

                            // On passe la liste des synonymes dans une boucle, et on les ajoutes un par un à l'objet (ArrayCollection)
                            foreach ($synonymes as $synonym) {
                                $species->addSynonym($synonym);
                            }
                        }
                    } else {
                        $output->writeln('<error>This ID doesn\'t match on a species.</error>');
                        return;
                    }
                } else {
                    $output->writeln('<error>This ID doesn\'t exist.</error>');
                    return;
                }
            }
            // Sinon, l'utilisateur ne renseigne pas de TaxId, alors on rempli tout manuellement
            else {
                // Scientificname
                $scientificNameQuestion = new Question("\nPlease enter the scientific name of the species:\n");
                $species->setScientificName($helper->ask($input, $output, $scientificNameQuestion));

                // Genus
                $genusQuestion = new Question("\nPlease enter the genus of the species:\n");
                $species->setGenus($helper->ask($input, $output, $genusQuestion));

                // Species
                $speciesQuestion = new Question("\nPlease enter the name of the species:\n");
                $species->setSpecies($helper->ask($input, $output, $speciesQuestion));

                // Lineage
                $lineageQuestion = new Question("\nPlease enter the lineage of the species:\n");
                $species->setLineage($helper->ask($input, $output, $lineageQuestion));

                // geneticCode
                $geneticCodeQuestion = new Question("\nPlease enter the genetic code of the species: (default: 1)\n", 1);
                $species->setGeneticCode($helper->ask($input, $output, $geneticCodeQuestion));

                // mitoCode
                $mitoCodeQuestion = new Question("\nPlease enter the mito code of the species: (default: 3)\n", 3);
                $species->setMitoCode($helper->ask($input, $output, $mitoCodeQuestion));

                // Synonymes
                $synonymesQuestion = new Question("\nPlease enter synonymes of the species: (use \"; \" as separator)(default: null)\n", null);
                $synonymes = $helper->ask($input, $output, $synonymesQuestion);
                if ($synonymes) {
                    $synonymes = explode("; ", $synonymes);
                    foreach ($synonymes as $synonym) {
                        $species->addSynonym($synonym);
                    }
                }
            }

            // Ici, on retrouve les champs communs, qui sont nécessaires pour le mode manuel, et auto avec l'API

            // Description
            $descriptionQuestion = new Question("\nPlease enter the description of the species: (default: null)\n", null);
            $species->setDescription($helper->ask($input, $output, $descriptionQuestion));

            // On fait un résumé des données récupérées
            $userData = "Clade:\t\t\t" . $species->getClade()->getName() . "\n";
            $userData .= "Scientific name:\t" . $species->getScientificName() . "\n";
            $userData .= "Genus:\t\t\t" . $species->getGenus() . "\n";
            $userData .= "Species:\t\t" . $species->getSpecies() . "\n";
            $userData .= "Lineage:\t\t" . $species->getLineage() . "\n";
            $userData .= "Genetic Code:\t\t" . $species->getGeneticCode() . "\n";
            $userData .= "Mito Code:\t\t" . $species->getMitoCode() . "\n";
            $userData .= "Synonymes:\n";
            if (null !== $species->getSynonymes()) {
                foreach ($species->getSynonymes() as $synonym) {
                    $userData .= "\t\t\t* " . $synonym . "\n";
                }
            }
            $userData .= "Description:\t\t" . $species->getDescription() . "\n";
            $userData .= "TaxId:\t\t\t" . $species->getTaxid() . "\n";

            $output->writeln("\n" . $userData);

            // On demande à l'utilisateur si les données sont bonnes ou pas
            $confirmQuestion = new ConfirmationQuestion("Is it correct ? (y/N)\n", false);
            // Si les données ne sont pas bonnes, on recommence la boucle
            // On supprime l'argument NCBI, pour remplir manuellement les champs
            // On détruit l'objet Species
            if (!$helper->ask($input, $output, $confirmQuestion)) {
                $confirm = false;
                $input->setArgument('ncbi', null);
                unset($species);
            } else {
                $confirm = true;
            }
        } while(!$confirm);

        // On persiste l'objet
        $em->persist($species);
        $em->flush();

        $output->writeln('<info>The species was successfully added</info>');
    }
}
