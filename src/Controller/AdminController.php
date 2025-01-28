<?php

namespace App\Controller;

use App\Entity\Bilera;
use App\Entity\Bisita;
use App\Entity\Langilea;
use App\Form\BileraType;
use App\Form\LangileaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $langileak = $this->em->getRepository(Langilea::class)->createQueryBuilder('b')
            ->where('b.Irteera = false')
            ->orderBy('b.Data', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/langilea.html.twig', [
            'langileak' => $langileak,
        ]);
    }

    #[Route('/Langilea/sortulangile', name:'app_langilea_sortulangile')]
    public function sortulangile(Request $request): Response
    {
        $langilea = new Langilea();
        $form = $this->createForm(LangileaType::class, $langilea);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Procesar la firma
            $signatureData = $request->request->get('signature-data');
            if ($signatureData) {
                // Decodificar la imagen base64
                $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
                $signatureData = base64_decode($signatureData);

                // Generar un nombre único para la imagen
                $signatureName = uniqid('signature_') . '.png';

                // Guardar la imagen en la ruta public/uploads/signatures
                $signaturePath = $this->getParameter('kernel.project_dir') . '/public/uploads/signatures/' . $signatureName;
                file_put_contents($signaturePath, $signatureData);

                // Guardar el nombre de la imagen en el campo firma
                $langilea->setFirma($signatureName);
            }

            // Establecer la fecha actual y guardar en la base de datos
            $langilea->setIrteera(false);
            $langilea->setData(new \DateTime('now'));
            $this->em->persist($langilea);
            $this->em->flush();

            return $this->redirectToRoute('app_admin_langilea');
        }

        return $this->render('admin/langileaSortu.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/langilea/{id}/update-irteera', name: 'langilea_update_irteera', methods: ['POST'])]
    public function updateIrteera(int $id, EntityManagerInterface $em): RedirectResponse
    {
        // Obtener el langilea por su ID
        $langilea = $em->getRepository(Langilea::class)->find($id);

        // Si no se encuentra, redirigir con un mensaje de error
        if (!$langilea) {
            $this->addFlash('danger', 'Langilea ez da aurkitu.');
            return $this->redirectToRoute('app_admin_langilea');
        }

        // Cambiar el valor del campo irteera
        $langilea->setIrteera(true);

        // Guardar los cambios en la base de datos
        $em->persist($langilea);
        $em->flush();

        // Mensaje de éxito
        $this->addFlash('success', 'Langilearen irteera eguneratu da.');
        return $this->redirectToRoute('app_admin_langilea');
    }

    #[Route('/bisitak', name: 'app_admin_bisitak')]
    public function Bisitak(Request $request): Response
    {
        $bisitak = $this->em->getRepository(Bisita::class)->createQueryBuilder('b')
            ->where('b.Itxita = false')
            ->orderBy('b.Data', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/Bisitak.html.twig', [
            'bisitak' => $bisitak,
        ]);
    }
    #[Route('/bisitak/{id}/update-bisita', name: 'app_bisita_update_bisita', methods: ['POST'])]
    public function updateBisita(int $id, EntityManagerInterface $em): RedirectResponse
    {
        // Obtener el langilea por su ID
        $bisita = $em->getRepository(Bisita::class)->find($id);

        // Si no se encuentra, redirigir con un mensaje de error
        if (!$bisita) {
            $this->addFlash('danger', 'Bisita ez da aurkitu.');
            return $this->redirectToRoute('app_admin_bisitak');
        }

        // Cambiar el valor del campo irteera
        $bisita->setItxita(true);

        // Guardar los cambios en la base de datos
        $em->persist($bisita);
        $em->flush();

        // Mensaje de éxito
        $this->addFlash('success', 'Bisita itxi da.');
        return $this->redirectToRoute('app_admin_bisitak');
    }

    #[Route('/exportatu', name: 'app_exportatu')]
    public function exportatu(): Response
    {
        return $this->render('admin/exportatu.html.twig');
    }
    #[Route('/egutegia', name: 'app_egutegia')]
    public function calendar(): Response
    {
        $year = (new \DateTime())->format('Y');
        return $this->render('admin/Egutegia.html.twig', [
            'year' => $year,
        ]);
    }
}
