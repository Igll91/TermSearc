<?php

namespace App\Repository;

use App\Entity\TermSearchResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TermSearchResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method TermSearchResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method TermSearchResult[]    findAll()
 * @method TermSearchResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TermSearchResultRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TermSearchResult::class);
    }

    /**
     * Get object by term or create new one.
     *
     * @param string $term Term used to search DB.
     * @return TermSearchResult Object from DB or newly created one.
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOrCreateByTerm(string $term)
    {
        $result = $this->createQueryBuilder('t')
            ->select(' partial t.{id, term, score, updated}')
            ->andWhere('t.term = :term')
            ->setParameter('term', $term)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result ? $result : new TermSearchResult($term);
    }
}
