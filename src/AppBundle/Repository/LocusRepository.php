<?php

namespace AppBundle\Repository;

/**
 * LocusRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LocusRepository extends \Doctrine\ORM\EntityRepository
{
    public function findLocus($locusName)
    {
        $query = $this->createQueryBuilder('locus')
            ->leftJoin('locus.features', 'features')
                ->addSelect('features')
            ->leftJoin('features.productsFeatures', 'products')
                ->addSelect('products')
            ->leftJoin('locus.chromosome', 'chromosome')
                ->addSelect('chromosome')
            ->leftJoin('chromosome.dnaSequence', 'dnaSequence')
                ->addSelect('dnaSequence')
            ->leftJoin('chromosome.strain', 'strain')
                ->addSelect('strain')
            ->leftJoin('strain.species', 'species')
                ->addSelect('species')
            ->where('locus.name = :locusName')
                ->setParameter('locusName', $locusName)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findLocusFromFeature($featureName)
    {
        $query = $this->createQueryBuilder('locus')
            ->leftJoin('locus.features', 'features')
                ->addSelect('features')
            ->leftJoin('features.productsFeatures', 'products')
                ->addSelect('products')
            ->leftJoin('locus.chromosome', 'chromosome')
                ->addSelect('chromosome')
            ->leftJoin('chromosome.dnaSequence', 'dnaSequence')
                ->addSelect('dnaSequence')
            ->leftJoin('chromosome.strain', 'strain')
                ->addSelect('strain')
            ->leftJoin('strain.species', 'species')
                ->addSelect('species')
            ->where('features.name = :featureName')
                ->setParameter('featureName', $featureName)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findLocusFromProduct($productName)
    {
        $query = $this->createQueryBuilder('locus')
            ->leftJoin('locus.features', 'features')
            ->addSelect('features')
            ->leftJoin('features.productsFeatures', 'products')
            ->addSelect('products')
            ->leftJoin('locus.chromosome', 'chromosome')
            ->addSelect('chromosome')
            ->leftJoin('chromosome.dnaSequence', 'dnaSequence')
            ->addSelect('dnaSequence')
            ->leftJoin('chromosome.strain', 'strain')
            ->addSelect('strain')
            ->leftJoin('strain.species', 'species')
            ->addSelect('species')
            ->where('products.name = :productName')
            ->setParameter('productName', $productName)
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}