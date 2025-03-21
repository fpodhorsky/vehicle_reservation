<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/user', name: 'app_user_')]
final class UserController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/my-reservations', name: 'reservations')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        $search = $request->get("search");
        $monthsBack = $request->get("monthsBack");

        /** @var ReservationRepository $reservationRepository */
        $reservationRepository = $this->entityManager->getRepository(Reservation::class);

        $query = match ($search) {
            "all" => $reservationRepository->findAllBookingsBy($user, intval($monthsBack)),
            "old" => $reservationRepository->findOldBookingsBy($user, intval($monthsBack)),
            default => $reservationRepository->findNewBookingsBy($user),
        };

        $reservations = $query->getQuery()->execute();

        if (empty($reservations)) {
            $this->addFlash(
                'warning',
                'Zatím nemáte žádné rezervace.'
            );
        }

        return $this->render('user/reservations.html.twig', array(
            'reservations' => $reservations,
        ));
    }
}
