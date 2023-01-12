<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManager;
use App\Model\User;

class UserService
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
     * @param User $user
     * @return User
     */
    public function save(User $user): User {
        $user_ = $this->userAlreadyExists($user->getEmail());

        if($user_){
            $user_->setEmail($user->getEmail());
            $user_->setName($user->getName());
            $this->em->flush();

            return $user_->getId();
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function validateEmail(string $email): bool {
        $regex = "/^([a-zA-Z0-9\.]+@+[a-zA-Z]+(\.)+[a-zA-Z]{2,3})$/";
        return preg_match($regex, $email) ? true : false;
    }

    /**
     * @param string $email
     * @return bool | User
     */
    public function userAlreadyExists(string $email): User | bool {
        $queryBuilder = $this->em->createQueryBuilder();
        $query = $queryBuilder
                ->select('u')
                ->from(User::class, 'u')
                ->where('u.email = :email')
                ->setParameter('email', $email)
                ->getQuery();
        $users = $query->getResult();

        return count($users) !== 0 ? $users[0] : false;
    }
    
}

?>