<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Chromosome;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ChromosomeController extends Controller
{
    /**
     * @Route("/db/{species_slug}/{strain_slug}/{chromosome_slug}", name="chromosome_view")
     * @Route("/chromosome/{chromosome_slug}")
     * @Entity("chromosome", expr="repository.getChromosomeWithStrainAndSpecies(chromosome_slug)")
     * @Security("is_granted('VIEW', chromosome.getStrain())")
     */
    public function viewAction(Chromosome $chromosome)
    {
        return $this->render('chromosome/view.html.twig', [
           'chromosome' => $chromosome,
        ]);
    }
}
