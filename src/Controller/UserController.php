<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use App\Form\UserEditType;
use App\Repository\ReservationRepository;
use Doctrine\DBAL\Exception;
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

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/list', name: 'list')]
    public function list(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('user/list.html.twig', array(
            'users' => $users,
        ));
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, int $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if ($user->getId() == $this->getUser()->getId()) {
            $this->addFlash(
                'warning',
                'Právě editujete sebe! Pozor na uživatelská práva!'
            );
        }

        $form = $this->createForm(UserEditType::class, $user);

        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->addFlash(
                    'success',
                    'Uživatel byl úspěšně změněn.'
                );

                return $this->redirectToRoute('app_user_list');
            }
        } catch (Exception $e) {
            $this->addFlash(
                'danger',
                "Uživatel nemohl být změněn. Ujistěte se, zda login již nepoužívá někdo jiný."
            );
        }

        return $this->render('user/edit.html.twig', array(
            'form' => $form->createView(),

        ));
    }
}
