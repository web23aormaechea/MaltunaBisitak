<?php

namespace App\Controller;

use App\Entity\Bilera;
use App\Entity\Bisitaria;
use App\Entity\Langilea;
use App\Form\BileraType;
use App\Form\BisitariaBileraType;
use App\Form\LangileaType;
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

    #[Route('/bilera', name: 'app_admin_bilera')]
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
            $this->addFlash('success', 'Bilera behar bezala sortu da!');
            return $this->redirectToRoute('app_admin_bilera');
        }

        $hoy = new \DateTime('today');
        $bilerak = $this->em->getRepository(Bilera::class)->createQueryBuilder('b')
            ->where('b.Data >= :hoy')
            ->setParameter('hoy', $hoy)
            ->orderBy('b.Data', 'ASC')
            ->getQuery()
            ->getResult();

        // Renderizar el formulario
        return $this->render('admin/bilera.html.twig', [
            'form' => $form->createView(),
            'bilerak' => $bilerak,
        ]);
    }

    #[Route('/bilera/delete/{id}', name: 'app_admin_bilera_delete', methods: ['POST'])]
    public function delete(Request $request, Bilera $bilera, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $bilera->getId(), $request->request->get('_token'))) {
            $em->remove($bilera);
            $em->flush();

            $this->addFlash('success', 'Bilera behar bezala ezabatu da.');
        }

        return $this->redirectToRoute('app_admin_bilera');
    }

    #[Route('/langilea', name: 'app_admin_langilea')]
    public function Langilea(Request $request): Response
    {
        $langilea = new Langilea();
        $form = $this->createForm(LangileaType::class, $langilea);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Guarda la firma en la base de datos, por ejemplo, en el campo 'firma'
            $firmaBase64 = $langilea->getFirma(); // Esto es la firma en base64

            // Si es necesario, puedes decodificarla a binario y guardarla como una imagen
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $firmaBase64));
            file_put_contents('ruta/donde/guardar/imagen.png', $imageData);

            // Guardar en la base de datos o hacer lo que sea necesario

            $this->em->persist($langilea);
            $this->em->flush();

            // Mostrar mensaje de éxito y redirigir
            $this->addFlash('success', 'Langilea behar bezala sortu da!');
            return $this->redirectToRoute('app_admin_langilea');
        }

        $langileak = $this->em->getRepository(Langilea::class)->createQueryBuilder('b')
            ->where('b.Irteera = false')
            ->orderBy('b.Data', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/langilea.html.twig', [
            'form' => $form->createView(),
            'langileak' => $langileak,
        ]);
    }

    #[Route('/langilea/{id}/update-irteera', name: 'langilea_update_irteera', methods: ['PATCH'])]
    public function updateIrteera(int $id, EntityManagerInterface $em): RedirectResponse
    {
        // Buscar el objeto Langilea por ID
        $langilea = $em->getRepository(Langilea::class)->find($id);

        if (!$langilea) {
            $this->addFlash('error', 'Langilea ez da aurkitu.');
            return $this->redirectToRoute('langilea_index');
        }

        // Actualizar el campo 'Irteera'
        $langilea->setIrteera(true);

        // Guardar cambios en la base de datos
        $em->persist($langilea);
        $em->flush();

        // Mensaje de éxito
        $this->addFlash('success', 'Langilearen irteera markatu da.');
        return $this->redirectToRoute('langilea_index');
    }

}
