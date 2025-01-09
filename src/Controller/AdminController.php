<?php

namespace App\Controller;

use App\Entity\Bilera;
use App\Form\BileraType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class AdminController extends AbstractController
{
    private $em;

    /**
     * @param $em
     * @return
     */
    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/Bilera', name: 'app_admin_bilera')]
    public function Bilera(Request $request): Response
    {
        // Crear una nueva instancia de Bilera
        $bilera = new Bilera();

        // Crear el formulario asociado a la entidad
        $form = $this->createForm(BileraType::class, $bilera);

        // Manejar la solicitud
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Guardar los datos en la base de datos
            $this->em->persist($bilera);
            $this->em->flush();

            // Mostrar mensaje de éxito y redirigir
            $this->addFlash('success', 'Bilera creada correctamente!');
            return $this->redirectToRoute('app_admin_bilera');
        }

        // Renderizar el formulario
        return $this->render('admin/bilera.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
