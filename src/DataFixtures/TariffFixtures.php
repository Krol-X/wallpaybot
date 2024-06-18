<?php

namespace App\DataFixtures;

use App\Entity\Tariff;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TariffFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tariff = new Tariff();
        $tariff->setName('Базовый');
        $tariff->setPrice(100);
        $tariff->setDiscountPercentage(10);
        $manager->persist($tariff);

        $manager->flush();
    }
}
