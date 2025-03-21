<?php

namespace App\DataFixtures;

use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VehicleFixture extends Fixture
{
    public const VEHICLE_BMW_REFERENCE = 'vehicle-bmw';
    public const VEHICLE_SKODA_REFERENCE = 'vehicle-skoda';
    public const VEHICLE_AUDI_REFERENCE = 'vehicle-audi';
    public const VEHICLE_FORD_REFERENCE = 'vehicle-ford';

    public function load(ObjectManager $manager): void
    {
        $vf = new \ReflectionClass(VehicleFixture::class);

        foreach ($vf->getConstants() as $key => $value) {
            if (str_contains($value, 'vehicle')) {
                $vehicle = new Vehicle();
                $vehicle->setSpz(substr(md5(microtime()), rand(0, 26), 7));
                $vehicle->setNote(str_replace('vehicle-', '', $value));
                $vehicle->setIsDeactivated(0);

                $this->addReference($value, $vehicle);

                $manager->persist($vehicle);
            }
        }

        $manager->flush();
    }
}
