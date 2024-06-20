<?php

namespace App\DataFixtures;

use App\Entity\Tariff;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class TariffFixtures extends Fixture
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $tariffRepository = $this->managerRegistry->getRepository(Tariff::class);

        if (!$tariffRepository->findOneBy([])) {
            $tariff = new Tariff();
            $tariff->setName('Базовый');
            $tariff->setPrice(100);
            $tariff->setDiscountPercentage(10);
            $manager->persist($tariff);

            $manager->flush();
        }
    }
}
