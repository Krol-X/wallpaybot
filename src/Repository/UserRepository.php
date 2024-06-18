<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $em
    )
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Create new user
     *
     * @param array $data data for new user
     * @return User
     */
    public function createUser(array $data): User
    {
        $user = $this->find($data['id']);

        if ($user) {
            // todo: warning
        }

        $user = new User();
        $user->setId($data['id'])
            ->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setUsername($data['username'])
            ->setLanguageCode($data['language_code']);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
