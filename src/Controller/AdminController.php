<?php

namespace App\Controller;

use App\Entity\Bilera;
use App\Entity\Bisita;
use App\Entity\Bisitaria;
use App\Entity\Langilea;
use App\Form\BileraType;
use App\Form\LangileaType;
use App\Entity\Egutegia;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;



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
    #[Route('/exportatu/datuak', name: 'app_exportatu_datuak', methods: ['POST'])]
    public function getExportData(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['selection'], $data['dateRange']) || empty($data['selection']) || empty($data['dateRange'])) {
            return new JsonResponse(['error' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
        }

        $selection = $data['selection'];
        [$startDate, $endDate] = explode(' - ', $data['dateRange']);
        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        $result = [];

        switch ($selection) {
            case 'Bilera':
                $query = $entityManager->createQuery(
                    "SELECT b.Izena, b.Lekua, b.Data 
                 FROM App\Entity\Bilera b
                 WHERE b.Data BETWEEN :startDate AND :endDate"
                );
                break;

            case 'Bisita':
                $query = $entityManager->createQuery(
                    "SELECT b.Izena, b.Nondik, b.Data 
                 FROM App\Entity\Bisita b
                 WHERE b.Data BETWEEN :startDate AND :endDate"
                );
                break;

            case 'Bisitaria':
                $query = $entityManager->createQuery(
                    "SELECT bi.Izena, bi.Abizena, bi.Nondik, bi.Email, 
                        COALESCE(bs.Data, bl.Data) AS data
                 FROM App\Entity\Bisitaria bi
                 LEFT JOIN bi.Bisita bs
                 LEFT JOIN bi.Bilera bl
                 WHERE (bs.Data BETWEEN :startDate AND :endDate) 
                    OR (bl.Data BETWEEN :startDate AND :endDate)"
                );
                break;

            case 'Langilea':
                $query = $entityManager->createQuery(
                    "SELECT l.Izena, l.Abizena, l.Telefonoa, l.Nondik, l.Data, l.Firma 
                 FROM App\Entity\Langilea l
                 WHERE l.Data BETWEEN :startDate AND :endDate"
                );
                break;

            default:
                return new JsonResponse(['error' => 'Selección no válida'], Response::HTTP_BAD_REQUEST);
        }

        $query->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        $result = $query->getResult();

        return new JsonResponse($result);
    }

    #[Route('/exportatu/excel', name: 'app_exportatu_excel', methods: ['POST'])]
    public function exportToExcel(Request $request, EntityManagerInterface $entityManager): StreamedResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['selection'], $data['dateRange']) || empty($data['selection']) || empty($data['dateRange'])) {
            return new JsonResponse(['error' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
        }

        $selection = $data['selection'];
        [$startDate, $endDate] = explode(' - ', $data['dateRange']);
        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        $result = [];
        $columns = [];

        switch ($selection) {
            case 'Bilera':
                $query = $entityManager->createQuery(
                    "SELECT b.Izena, b.Lekua, b.Data 
                 FROM App\Entity\Bilera b
                 WHERE b.Data BETWEEN :startDate AND :endDate"
                );
                $columns = ['Izena', 'Lekua', 'Data'];
                break;

            case 'Bisita':
                $query = $entityManager->createQuery(
                    "SELECT b.Izena, b.Nondik, b.Data 
                 FROM App\Entity\Bisita b
                 WHERE b.Data BETWEEN :startDate AND :endDate"
                );
                $columns = ['Izena', 'Nondik', 'Data'];
                break;

            case 'Bisitaria':
                $query = $entityManager->createQuery(
                    "SELECT bi.Izena, bi.Abizena, bi.Nondik, bi.Email, 
                        COALESCE(bs.Data, bl.Data) AS data
                 FROM App\Entity\Bisitaria bi
                 LEFT JOIN bi.Bisita bs
                 LEFT JOIN bi.Bilera bl
                 WHERE (bs.Data BETWEEN :startDate AND :endDate) 
                    OR (bl.Data BETWEEN :startDate AND :endDate)"
                );
                $columns = ['Izena', 'Abizena', 'Nondik', 'Email'];
                break;

            case 'Langilea':
                $query = $entityManager->createQuery(
                    "SELECT l.Izena, l.Abizena, l.Telefonoa, l.Nondik, l.Data, l.Firma 
                 FROM App\Entity\Langilea l
                 WHERE l.Data BETWEEN :startDate AND :endDate"
                );
                $columns = ['Izena', 'Abizena', 'Telefonoa', 'Nondik', 'Data', 'Firma'];
                break;

            default:
                return new JsonResponse(['error' => 'Selección no válida'], Response::HTTP_BAD_REQUEST);
        }

        $query->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        $result = $query->getResult();

        // Crear un nuevo documento de Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Escribir encabezados
        foreach ($columns as $colIndex => $colName) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1); // Convierte número en letra (1 -> A, 2 -> B, etc.)
            $sheet->setCellValue($colLetter . '1', $colName);
        }

        // Escribir datos
        foreach ($result as $rowIndex => $rowData) {
            foreach ($columns as $colIndex => $colName) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
                $cellCoordinate = $colLetter . ($rowIndex + 2);

                if ($colName === 'Firma') {
                    $imagePath = __DIR__ . "/../../public/uploads/signatures/" . $rowData['Firma']; // Ruta absoluta
                    if (file_exists($imagePath)) {
                        $drawing = new Drawing();
                        $drawing->setPath($imagePath);
                        $drawing->setCoordinates($cellCoordinate);
                        $drawing->setWorksheet($sheet);
                        $drawing->setWidth(70);  // Ajusta el ancho de la imagen
                        $drawing->setHeight(50); // Ajusta la altura de la imagen

                        // 🔹 Aumentar la altura de la fila para que la imagen se vea bien
                        $sheet->getRowDimension($rowIndex + 2)->setRowHeight(50);
                    } else {
                        $sheet->setCellValue($cellCoordinate, 'Sin firma');
                    }
                    // 🔹 Ajustar el ancho de la columna "Firma" solo una vez
                    $sheet->getColumnDimension($colLetter)->setWidth(15);
                } else {
                    $sheet->setCellValue($cellCoordinate, $rowData[$colName] ?? '');
                }
            }
        }

        // Generar respuesta de descarga
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'datos_exportados.xlsx');

        return $response;
    }

    #[Route('/egutegia', name: 'app_egutegia')]
    public function calendar(): Response
    {
        // Obtener el año actual y calcular el rango del calendario escolar
        $currentYear = (int)date('Y');
        $currentMonth = (int)date('m');

// Si estamos antes de septiembre, el rango será de septiembre del año anterior hasta julio del año actual
        if ($currentMonth < 9) {
            $startYear = $currentYear - 1;
            $endYear = $currentYear;
        } else {
            $startYear = $currentYear;
            $endYear = $currentYear + 1;
        }

// Definir las fechas del calendario escolar (desde septiembre hasta julio)
        $inicio = new \DateTime("first day of September $startYear");
        $fin = new \DateTime("last day of July $endYear");

// Consultar las fechas guardadas en la base de datos (Egutegia) dentro de este rango
        $dates = $this->em->getRepository(Egutegia::class)->createQueryBuilder('e')
            ->where('e.Data BETWEEN :inicio AND :fin')
            ->setParameter('inicio', $inicio)
            ->setParameter('fin', $fin)
            ->getQuery()
            ->getResult();

// Pasar las fechas seleccionadas como objetos DateTime
        $fechasSeleccionadas = [];
        foreach ($dates as $egutegia) {
            // Convertimos las fechas a formato Y-m-d para que coincidan con el formato que usas en el calendario
            $fechasSeleccionadas[] = $egutegia->getData()->format('Y-m-d');
        }

// Redirigir y cargar el calendario con las fechas seleccionadas
        return $this->render('admin/Egutegia.html.twig', [
            'inicio' => $inicio,
            'fin' => $fin,
            'fechasSeleccionadas' => $fechasSeleccionadas
        ]);
    }





    #[Route('/egutegia/gorde', name: 'app_egutegia_gorde', methods: ['POST'])]
    public function save(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fechas = $request->request->get('fechas');

        if ($fechas) {
            $fechasArray = explode(',', $fechas);

            foreach ($fechasArray as $fecha) {
                if (!empty($fecha)) {
                    $egutegia = new Egutegia(); // Instancia de la entidad
                    $egutegia->setData(new \DateTime($fecha));

                    $entityManager->persist($egutegia);
                }
            }

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_egutegia'); // Redirigir de nuevo al calendario
    }

    #[Route('/egutegia/ezabatu', name: 'app_egutegia_ezabatu', methods: ['POST'])]
    public function borrarDia(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Obtener la fecha enviada desde el frontend
        $data = json_decode($request->getContent(), true);
        $date = $data['date'] ?? null;

        if ($date) {
            // Convertir la cadena de fecha a un objeto DateTime
            $dateObj = \DateTime::createFromFormat('Y-m-d', $date);

            // Verificar si la conversión fue exitosa
            if (!$dateObj) {
                return new JsonResponse(['success' => false, 'message' => 'Fecha no válida'], 400);
            }

            // Buscar el registro en la base de datos
            $dayToDelete = $entityManager->getRepository(Egutegia::class)->findOneBy(['Data' => $dateObj]);

            if ($dayToDelete) {
                // Eliminar el día encontrado
                $entityManager->remove($dayToDelete);
                $entityManager->flush();

                // Devolver una respuesta exitosa sin mensaje
                return new JsonResponse(['success' => true]);
            }
        }

        // Si no se proporciona una fecha válida o no se encuentra el día, retornar error (sin mensajes explícitos)
        return new JsonResponse(['success' => false], 400);
    }
}
