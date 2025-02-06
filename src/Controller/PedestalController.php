<?php

namespace App\Controller;

use App\Entity\Bilera;
use App\Entity\Bisita;
use App\Entity\Bisitaria;
use App\Form\BisitariaBileraType;
use App\Form\BisitariaPedestalType;
use App\Form\BisitaType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PedestalController extends AbstractController
{
    private $em;

    /**
     * @param $em
     * @return
     */
    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/pedestal', name: 'app_pedestal')]
    public function index(): Response
    {

        return $this->render('pedestal/index.html.twig', [

        ]);
    }

    #[Route('/pedestal/bilera', name: 'app_pedestal_bilera')]
    public function Bilera(): Response
    {
        $hoy = new \DateTime('today');
        $bilerak = $this->em->getRepository(Bilera::class)->createQueryBuilder('b')
            ->where('b.Data = :hoy')
            ->setParameter('hoy', $hoy)
            ->orderBy('b.Data', 'ASC')
            ->getQuery()
            ->getResult();

        // Renderizar el formulario
        return $this->render('pedestal/bilera.html.twig', [
            'bilerak' => $bilerak,
        ]);
    }

    #[Route('/pedestal/bilera/{id}/bisitaria', name: 'app_pedestal_bilera_bisitaria_form', methods: ['GET', 'POST'])]
    public function bisitariaForm(Request $request, Bilera $bilera): Response
    {
        $bisitaria = new Bisitaria();
        $bisitaria->setBilera($bilera); // Relacionar el bisitaria con la bilera actual

        // Crear y manejar el formulario
        $form = $this->createForm(BisitariaBileraType::class, $bisitaria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Guardar el bisitaria en la base de datos
            $this->em->persist($bisitaria);
            $this->em->flush();

            // Responder con un script que cierre el popup y recargue la ventana principal
            return $this->redirectToRoute('popup_success'); // Asegúrate de tener esta ruta definida correctamente
        }

        // Si no es válido o no se ha enviado, renderiza el formulario
        return $this->render('pedestal/BisitariaBilera.html.twig', [
            'form' => $form->createView(),
            'bilera' => $bilera,
        ]);
    }

    #[Route('/popup_success', name: 'popup_success')]
    public function popupSuccess(): Response
    {
        return $this->render('pedestal/popup_success.html.twig');
    }

    #[Route('/pedestal/bisita', name: 'app_pedestal_bisita')]
    public function bisitaForm(Request $request): Response
    {
        $bisita = new Bisita();

        $form = $this->createForm(BisitaType::class, $bisita);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bisita->setItxita(false);
            $bisita->setData(new \DateTime('now'));
            $this->em->persist($bisita);
            $this->em->flush();
            // Redirigir a la nueva página pasando los datos de la visita creada
            return $this->redirectToRoute('app_pedestal_bisita_bisitaria', [
                'id' => $bisita->getId(),  // Aquí pasas el ID en lugar del objeto completo
            ]);
        }
        return $this->render('pedestal/Bisita.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/pedestal/bisita/bisitaria/{id}', name: 'app_pedestal_bisita_bisitaria')]
    public function bisitaBisitariaForm(Request $request, Bisita $bisita): Response
    {
        // Crear un nuevo objeto Bisitaria relacionado con la Bisita
        $bisitaria = new Bisitaria();
        $bisitaria->setBisita($bisita);

        // Crear el formulario para Bisitaria
        $form = $this->createForm(BisitariaPedestalType::class, $bisitaria);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persistir el nuevo Bisitaria en la base de datos
            $this->em->persist($bisitaria);
            $this->em->flush();

            // Redirigir al mismo formulario para permitir agregar más registros
            return $this->redirectToRoute('app_pedestal_bisita_bisitaria', [
                'id' => $bisita->getId(),  // Aquí pasas el ID en lugar del objeto completo
            ]);
        }

        // Obtener la lista de Bisitaria relacionados con la Bisita
        $bisitariak = $this->em->getRepository(Bisitaria::class)
            ->createQueryBuilder('b')
            ->where('b.Bisita = :bisita')
            ->setParameter('bisita', $bisita->getId())
            ->getQuery()
            ->getResult();

        $webUrl = $_ENV['WEB_URL'];
        $qrUrl = $webUrl . 'bisita/pedestal/' . $bisita->getId();  // Si 'bisita' es un objeto
        // Renderizar la vista con el formulario y la lista de Bisitaria
        return $this->render('pedestal/Bisitaria.html.twig', [
            'form' => $form->createView(),
            'qrUrl' => $qrUrl,
            'bisita' => $bisita,
            'bisitariak' => $bisitariak,
        ]);
    }

    #[Route('/pedestal/bisita/imprimatu/{id}', name: 'app_pedestal_bisita_imprimatu')]
    public function imprimatuBisitaria(int $id, EntityManagerInterface $entityManager): Response
    {
        $bisitaria = $entityManager->getRepository(Bisitaria::class)->find($id);
        if (!$bisitaria) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $this->enviarAImpresora($bisitaria);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[Route('/pedestal/bisita/lista/{id}', name: 'app_pedestal_bisita_lista')]
    public function imprimatuLista(int $id, EntityManagerInterface $entityManager): Response
    {
        $bisita = $entityManager->getRepository(Bisita::class)->find($id);
        if (!$bisita) {
            return new Response('Bisita no encontrada', Response::HTTP_NOT_FOUND);
        }

        $bisitariak = $entityManager->getRepository(Bisitaria::class)->findBy(['Bisita' => $bisita]);
        foreach ($bisitariak as $bisitaria) {
            $this->enviarAImpresora($bisitaria);
            usleep(1000000); // Añadir 0.5 segundos de delay entre impresiones
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    private function enviarAImpresora(Bisitaria $bisitaria): void
    {
        $contenido = "\n\n          " . $bisitaria->getIzena() . " " . $bisitaria->getAbizena() . "\n";
        $contenido .= "\n          " . $bisitaria->getNondik();

        $tempFile = tempnam(sys_get_temp_dir(), 'print_');
        file_put_contents($tempFile, $contenido);

        $printerIp = '10.23.72.93';
        $printerName = 'ZPRINT';
        shell_exec("lpr -S $printerIp -P $printerName -d $tempFile");

        unlink($tempFile);
    }
}
