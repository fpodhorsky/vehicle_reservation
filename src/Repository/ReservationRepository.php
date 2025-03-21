<?php

namespace App\Repository;

use App\Entity\Reservation;
use DateMalformedIntervalStringException;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findOldBookingsBy(UserInterface $user, int $monthsBack = 12): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->where('r.user = :u')
            ->andWhere('r.date_from >= :date')
            ->andWhere('r.date_to <= :date_now')
            ->setParameters(new ArrayCollection(['u' => $user,
                'date' => $this->monthsBack($monthsBack),
                'date_now' => new DateTime()]))
            ->join('r.car', 'c')
            ->orderBy('r.date_from', 'DESC');

    }

    public function findNewBookingsBy(UserInterface $user): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->where('r.user = :u')
            ->andWhere('r.date_from >= :date')
            ->setParameters(new ArrayCollection(['u' => $user, 'date' => new DateTime()]))
            ->join('r.car', 'c')
            ->orderBy('r.date_from', 'DESC');
    }

    public function findAllNewBookings(int $monthsBack = 12)
    {
        return $this->createQueryBuilder('r')
            ->where('r.date_to >= :date')
            ->andWhere('r.date_from >= :date_from')
            ->setParameters(new ArrayCollection([
                'date_from' => $this->monthsBack($monthsBack),
                'date' => new DateTime()
            ]))
            ->join('r.car', 'c')
            ->join('r.user', 'u')
            ->orderBy('r.date_from', 'DESC');
    }

    //získá konflikty rezervace
    public function getConflicts($date_from, $date_to, $car)
    {
        $result = $this->createQueryBuilder('r')
            ->where('r.car = :car')
            ->andWhere('r.date_to >= :date_from')
            ->andWhere('r.date_from <= :date_to')
            ->setParameters(new ArrayCollection([
                'car' => $car,
                'date_from' => $date_from,
                'date_to' => $date_to
            ]))->getQuery()->getResult();

        return ($result);
    }

    public function findAllBookingsBy(UserInterface $user, int $monthsBack = 12): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->where('r.user = :user')
            ->andWhere('r.date_from >= :date')
            ->setParameters(new ArrayCollection([
                'user' => $user,
                'date' => $this->monthsBack($monthsBack)
            ]))
            ->join('r.car', 'c') // potřebujeme na paginaci :)
            ->orderBy('r.date_from', 'DESC');
    }

    public function findAllLimit(int $monthsBack = 12): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->where('r.date_from >= :date')
            ->setParameter('date', $this->monthsBack($monthsBack))
            ->join('r.car', 'c') // potřebujeme na paginaci :)
            ->join('r.user', 'u') // - || -
            ->orderBy('r.date_from', 'DESC');
    }

    public function findAll(): array
    {
        return $this->findBy([], ['date_from' => 'ASC']);
    }

    private function monthsBack(int $monthsBack): DateTime
    {
        $date = new DateTime();
        if ($monthsBack > 0) {
            $date->sub(new \DateInterval("P" . $monthsBack . "M"));
            return $date;
        }
        return $date;
    }
}
