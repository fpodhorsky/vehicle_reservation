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
#[Route('/reservation', name: 'app_reservation_')]
final class ReservationController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/list', name: 'list')]
    public function list(Request $request): Response
    {
        $search = $request->get("search");
        $monthsBack = $request->get("monthsBack");

        if (!$monthsBack)
            $monthsBack = 12;

        /** @var ReservationRepository $reservationRepository */
        $reservationRepository = $this->entityManager->getRepository(Reservation::class);

        $query = match ($search) {
            "all" => $reservationRepository->findAllLimit(intval($monthsBack)),
            default => $reservationRepository->findAllNewBookings(intval($monthsBack)),
        };

        $reservations = $query->getQuery()->execute();

        if (empty($reservations)) {
            $this->addFlash(
                'warning',
                'Neexistují žádné nadcházející rezervace.'
            );
        }

        return $this->render('reservation/list.html.twig', ['reservations' => $reservations]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Request $request, int $id): Response
    {
        /** @var ReservationRepository $reservationRepository */
        $reservationRepository = $this->entityManager->getRepository(Reservation::class);

        $referer = $request->headers->get('referer');

        $user = $this->getUser();

        $reservation = $reservationRepository->find($id);

        if (!$reservation) {
            $this->addFlash(
                'danger',
                'Rezervace neexistuje.'
            );


            if ($referer) {
                return $this->redirect($referer);
            }
        }

        $isYourBooking = $reservation->getUser()->getEmail() == $user->getEmail();
        $isAdmin = $this->isGranted("ROLE_ADMIN");

        if ($isYourBooking || $isAdmin) {
            $this->entityManager->remove($reservation);

            $this->addFlash(
                'success',
                'Rezervace úspěšně smazána.'
            );
            if ($isAdmin && !$isYourBooking) {
                $this->addFlash(
                    'info',
                    'Rezervace nebyla vaše. Vlastníkovi rezervace bude odeslán email.'
                );
            }
        } else {
            $this->addFlash(
                'danger',
                'Rezervace nemohla být smazána.'
            );
        }

        $this->entityManager->flush();

        if ($referer) {
            return $this->redirect($referer);
        };
    }
}
