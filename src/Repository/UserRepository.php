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
        private readonly EntityManagerInterface $entityManager
    )
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Find user by Telegram ID or create new, if exists
     *
     * @param int $telegramId
     * @param array $defaultData data for new user
     * @return User
     */
    public function findOrCreate(int $telegramId, array $defaultData = []): User
    {
        $user = $this->findOneBy(['telegram_id' => $telegramId]);

        if (!$user) {
            $user = new User();
            $user->setTelegramId($telegramId);

            // Заполнить пользователя данными по умолчанию
            if (isset($defaultData['first_name'])) {
                $user->setFirstName($defaultData['first_name']);
            }
            if (isset($defaultData['last_name'])) {
                $user->setLastName($defaultData['last_name']);
            }
            if (isset($defaultData['username'])) {
                $user->setUsername($defaultData['username']);
            }
            if (isset($defaultData['language_code'])) {
                $user->setLanguageCode($defaultData['language_code']);
            }
            if (isset($defaultData['profile_image'])) {
                $user->setProfileImage($defaultData['profile_image']);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $user;
    }


    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
