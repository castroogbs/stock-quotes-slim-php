<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\User;
use Doctrine\ORM\EntityManager;

class AuthService
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
     * @param string $user
     * @param string $password
     * @return User | bool
     */
    public function validateUser(string $user, string $password): User | bool {
        $queryBuilder = $this->em->createQueryBuilder();
        $query = $queryBuilder
                ->select('u')
                ->from(User::class, 'u')
                ->where('u.email = :email')
                ->andWhere('u.password = :password')
                ->setParameter('email', $user)
                ->setParameter('password', $password)
                ->getQuery();
        $users = $query->getResult();

        return count($users) !== 0 ? $users[0] : false;
    }


}

?>