<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\User;
use Doctrine\ORM\EntityManager;
use App\Model\StockQuoteResearch;

class StockQuoteResearchService
{
    /**
     * @var EntityManager $em;
     */
    private EntityManager $em;

     /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array {
        $queryBuilder = $this->em->createQueryBuilder();
        $query = $queryBuilder
				->select('r')
				->from(StockQuoteResearch::class, 'r')
                ->where('r.userId = :uid')
                ->setParameter('uid', $userId)
				->orderBy('r.date', 'desc')
				->getQuery();
        return $query->getArrayResult();
    }

    /**
     * @param StockQuoteResearch $research
     * @return StockQuoteResearch
     */
    public function save(StockQuoteResearch $research): StockQuoteResearch {
        $user = $this->em->find(User::class, $research->getUserId());
        $research->setUser($user);
        $this->em->persist($research);
        $this->em->flush();

        return $research;
    }



  
}

?>