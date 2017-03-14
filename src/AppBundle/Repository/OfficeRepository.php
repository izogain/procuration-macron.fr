<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Office;
use AppBundle\Entity\User;
use AppBundle\Search\QuerySanitizer;
use Doctrine\ORM\EntityRepository;

class OfficeRepository extends EntityRepository
{
    /**
     * @param int $id
     *
     * @return Office|null
     */
    public function findWithReferents($id)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.referents', 'r')
            ->where('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderByPostalCode()
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.address.postalCode', 'ASC');
    }

    /**
     * @param User $user
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderAllForReferent(User $user)
    {
        return $this->getQueryBuilderByPostalCode()
            ->innerJoin('o.referents', 'r')
            ->where('r = :user')
            ->setParameter('user', $user);
    }

    /**
     * @param string $term
     *
     * @return array
     */
    public function searchCity(?string $term)
    {
        if (strlen($term) < 2) {
            return [];
        }

        $keywords = QuerySanitizer::extractKeywords($term);

        $qb = $this->createQueryBuilder('a');
        $qb->select('a.address.cityName AS city', 'a.address.postalCode AS postalCode');
        $qb->groupBy('a.address.cityName', 'a.address.postalCode');

        $filter = $qb->expr()->orX();
        $relevancy = [];

        foreach ($keywords as $i => $value) {
            $filter->add('LOWER(a.address.cityName) LIKE :keyword_'.$i);
            $filter->add('LOWER(a.address.postalCode) LIKE :keyword_'.$i);

            $relevancy[] = '(CASE WHEN LOWER(a.address.cityName) LIKE :keyword_'.$i.'_start THEN 5 ELSE 0 END)';
            $relevancy[] = '(CASE WHEN LOWER(a.address.cityName) LIKE :keyword_'.$i.' THEN 2 ELSE 0 END)';

            $relevancy[] = '(CASE WHEN LOWER(a.address.postalCode) LIKE :keyword_'.$i.'_start THEN 5 ELSE 0 END)';
            $relevancy[] = '(CASE WHEN LOWER(a.address.postalCode) LIKE :keyword_'.$i.' THEN 2 ELSE 0 END)';

            $qb->setParameter('keyword_'.$i.'_start', $value.'%');
            $qb->setParameter('keyword_'.$i, '%'.$value.'%');
        }

        $qb->addSelect('('.implode(' + ', $relevancy).') AS HIDDEN relevancy');
        $qb->where($filter);
        $qb->orderBy('relevancy', 'DESC');

        return array_map([$this, 'formatCityResult'], $qb->getQuery()->getResult());
    }

    /**
     * @param null|string $cityName
     * @param null|string $postalCode
     *
     * @return array
     */
    public function findByCityAndPostalCode(?string $cityName, ?string $postalCode)
    {
        return $this->createQueryBuilder('o')
            ->select('o.name', 'o.id')
            ->where('LOWER(o.address.cityName) = :cityName')
            ->andWhere('LOWER(o.address.postalCode) = :postalCode')
            ->setParameter('cityName', $cityName)
            ->setParameter('postalCode', $postalCode)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param array $item
     *
     * @return array
     */
    private function formatCityResult(array $item): array
    {
        $label = $item['city'].' ('.$item['postalCode'].')';

        return [
            'label' => $label,
            'value' => $label,
            'city' => $item['city'],
            'postalCode' => $item['postalCode'],
        ];
    }
}
