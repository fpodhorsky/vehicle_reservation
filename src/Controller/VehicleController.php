<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Form\VehicleFormType;
use App\Repository\VehicleRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/vehicle', name: 'app_vehicle_')]
final class VehicleController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly VehicleRepository      $vehicleRepository
    )
    {
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/list', name: 'list')]
    public function list(): Response
    {
        return $this->render('vehicle/list.html.twig', [
            'vehicles' => $this->vehicleRepository->findAll()
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/create', name: 'create')]
    public function create(Request $request): Response
    {
        $vehicle = new Vehicle();

        $form = $this->createForm(VehicleFormType::class, $vehicle);

        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->persist($vehicle);
                $this->entityManager->flush();

                $this->addFlash(
                    'success',
                    'Vozidlo úspěšně přidáno'
                );
                return $this->redirectToRoute('app_vehicle_list');
            }
        } catch (Exception $e) {
            $this->addFlash(
                'danger',
                "Vozidlo nemohlo být přidáno. Ujistěte se, zda již není přidáno."
            );
        }

        return $this->render('vehicle/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, int $id): Response
    {
        $vehicle = $this->vehicleRepository->find($id);

        if (!$vehicle) {
            $this->addFlash(
                'danger',
                'Vozidlo neexistuje.'
            );

            return $this->redirectToRoute('app_vehicle_list');
        }

        $form = $this->createForm(VehicleFormType::class, $vehicle);

        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->persist($vehicle);
                $this->entityManager->flush();

                $this->addFlash(
                    'success',
                    'Změna vozidla byla úspěšná.'
                );

                return $this->redirectToRoute('cars');
            }
        } catch (Exception $e) {
            $this->addFlash(
                'danger',
                "Vozidlo nemohlo být změněno. Ujistěte se, zda již toto vozidlo se stejnou SPZ nebylo přidáno."
            );
        }

        return $this->render('vehicle/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(int $id): Response
    {
        try {
            $vehicle = $this->vehicleRepository->find($id);

            if (!$vehicle) {
                $this->addFlash(
                    'danger',
                    'Vozidlo neexistuje.'
                );

                return $this->redirectToRoute('app_vehicle_list');
            }

            $reservations = $vehicle->getReservations();
            $count = sizeof($reservations);

            $this->entityManager->remove($vehicle);
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                'Vozidlo ' . $vehicle->getNote() . ' úspěšně odstraněno. Počet rezervací odstraněno: ' . $count
            );

        } catch (Exception $e) {
            $this->addFlash(
                'danger',
                "Vozidlo nemohlo být smazáno. Ujistěte se, zda toto vozidlo existuje."

            );
        }

        return $this->redirectToRoute('app_vehicle_list');
    }
}
