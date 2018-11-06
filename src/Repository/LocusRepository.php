<?php
/**
 *    Copyright 2015-2018 Mathieu Piot.
 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */

namespace App\Repository;

use App\Entity\Strain;

/**
 * LocusRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LocusRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Get locus with all linked data.
     *
     * @param $locusName
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return mixed
     */
    public function findLocusWithAllData($slug)
    {
        $query = $this->createQueryBuilder('locus')
            ->where('locus.slug = :slug')
            ->innerJoin('locus.chromosome', 'chromosome')
                ->addSelect('chromosome')
            ->innerJoin('chromosome.strain', 'strain')
                ->addSelect('strain')
            ->innerJoin('strain.species', 'species')
                ->addSelect('species')
            ->leftJoin('strain.users', 'users')
                ->addSelect('users')
            ->leftJoin('locus.features', 'features')
                ->addSelect('features')
            ->leftJoin('features.productsFeatures', 'products_features')
                ->addSelect('products_features')
            ->leftJoin('locus.references', 'locus_references')
                ->addSelect('locus_references')
            ->leftJoin('strain.references', 'strain_references')
                ->addSelect('strain_references')
            ->orderBy('features.name', 'ASC')
            ->addOrderBy('products_features.name', 'ASC')
            ->setParameter('slug', $slug)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @param $locusName
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return mixed
     */
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

    /**
     * @param $featureName
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return mixed
     */
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

    /**
     * @param $productName
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return mixed
     */
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

    /**
     * @param $productName
     *
     * @return array
     */
    public function findLocusFromProductWithoutDnaSequence($productName)
    {
        $query = $this->createQueryBuilder('locus')
            ->leftJoin('locus.features', 'features')
                ->addSelect('features')
            ->leftJoin('features.productsFeatures', 'products')
                ->addSelect('products')
            ->leftJoin('locus.chromosome', 'chromosome')
                ->addSelect('chromosome')
            ->leftJoin('chromosome.strain', 'strain')
                ->addSelect('strain')
            ->leftJoin('strain.species', 'species')
                ->addSelect('species')
            ->where('products.name IN(:productName)')
                ->setParameter('productName', $productName)
            ->orderBy('locus.name', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param array $ids
     *
     * @return array
     */
    public function findLocusById(array $ids)
    {
        $query = $this->createQueryBuilder('locus')
            ->leftJoin('locus.chromosome', 'chromosome')
                ->addSelect('chromosome')
            ->leftJoin('chromosome.strain', 'strain')
                ->addSelect('strain')
            ->leftJoin('strain.species', 'species')
                ->addSelect('species')
            ->orderBy('locus.name', 'asc')
            ->where('locus.id IN (:id)')
                ->setParameter('id', $ids)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param Strain $strain
     *
     * @return array
     */
    public function findLocusFromStrain(Strain $strain)
    {
        $query = $this->createQueryBuilder('locus')
            ->leftJoin('locus.chromosome', 'chromosome')
                ->addSelect('chromosome')
            ->leftJoin('chromosome.strain', 'strain')
                ->addSelect('strain')
            ->leftJoin('strain.users', 'users')
                ->addSelect('users')
            ->where('strain = :strain')
                ->setParameter('strain', $strain->getId())

            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param $offset
     * @param $limit
     *
     * @return array
     */
    public function findPublicLocus($offset, $limit)
    {
        $query = $this->createQueryBuilder('locus')
            ->select('partial locus.{id, name, chromosome}, partial chromosome.{id, slug, strain}, partial strain.{id, slug, species}, partial species.{id, slug}')
            ->leftJoin('locus.chromosome', 'chromosome')
            ->leftJoin('chromosome.strain', 'strain')
            ->leftJoin('strain.species', 'species')
            ->where('strain.public = true')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return mixed
     */
    public function countPublicLocus()
    {
        $query = $this->createQueryBuilder('locus')
            ->select('COUNT(locus)')
            ->leftJoin('locus.chromosome', 'chromosome')
            ->leftJoin('chromosome.strain', 'strain')
            ->where('strain.public = true')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    public function createSearchQueryBuilder($entityAlias)
    {
        $qb = $this->createQueryBuilder($entityAlias)
            ->leftJoin($entityAlias.'.chromosome', 'chromosome')
                ->addSelect('chromosome')
            ->leftJoin('chromosome.strain', 'strain')
                ->addSelect('strain')
            ->leftJoin('strain.species', 'species')
                ->addSelect('species');

        return $qb;
    }
}