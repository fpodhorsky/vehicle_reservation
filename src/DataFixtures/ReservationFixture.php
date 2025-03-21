<?php

namespace App\DataFixtures;

use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Vehicle;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReservationFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $vehicles = [
            $this->getReference(VehicleFixture::VEHICLE_BMW_REFERENCE, Vehicle::class),
            $this->getReference(VehicleFixture::VEHICLE_AUDI_REFERENCE, Vehicle::class),
            $this->getReference(VehicleFixture::VEHICLE_SKODA_REFERENCE, Vehicle::class),
            $this->getReference(VehicleFixture::VEHICLE_FORD_REFERENCE, Vehicle::class),
        ];

        $userBasic = $this->getReference(UserFixture::USER_USER_REFERENCE, User::class);
        $userAdmin = $this->getReference(UserFixture::USER_ADMIN_REFERENCE, User::class);

        $startDate = new DateTime();
        $endDate = new DateTime();
        $endDate->modify('+3 hours');

        $reservationsCount = 6;

        for ($i = 1; $i <= $reservationsCount; $i++) {
            $dateModifier = "+" . $i . " days";

            $dateFrom = (clone $startDate)->modify($dateModifier);
            $dateTo = (clone $endDate)->modify($dateModifier);

            $user = ($i > $reservationsCount * 0.5) ? $userAdmin : $userBasic;

            $res = new Reservation();
            $res->setVehicle($vehicles[array_rand($vehicles)]);
            $res->setUser($user);
            $res->setNote($i . ". rezervace");
            $res->setDateFrom($dateFrom);
            $res->setDateTo($dateTo);

            $manager->persist($res);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            VehicleFixture::class,
        ];
    }
}
