<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function create(): Response
    {
        return $this->render('vehicle/create.html.twig', [
            'vehicle' => new Vehicle()
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/edit/{id}', name: 'edit')]
    public function edit(int $id): Response
    {
        $vehicle = $this->vehicleRepository->find($id);

        if (!$vehicle) {
            $this->addFlash(
                'danger',
                'Vozidlo neexistuje.'
            );

            return $this->redirectToRoute('app_vehicle_list');
        }

        return $this->render('vehicle/edit.html.twig', ['vehicle' => $vehicle]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(int $id): Response
    {
        $vehicle = $this->vehicleRepository->find($id);

        if (!$vehicle) {
            $this->addFlash(
                'danger',
                'Vozidlo neexistuje.'
            );

            return $this->redirectToRoute('app_vehicle_list');
        }

        $this->entityManager->remove($vehicle);
        $this->entityManager->flush();

        $this->addFlash(
            'danger',
            'Vozidlo ' . $id . ' bylo odstraněno.'
        );

        return $this->redirectToRoute('app_vehicle_list');
    }
}
