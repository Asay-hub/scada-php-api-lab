<?php

namespace App\Controller;

use App\Repository\DeviceRepository;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ExportController extends AbstractController
{
    #[Route('/export/pdf', name: 'site_export_pdf', methods: ['GET'])]
    public function pdf(DeviceRepository $deviceRepo): Response
    {
        $devices = $deviceRepo->findByFilter();

        $html = $this->renderView('site/report_pdf.html.twig', [
            'devices' => $devices,
            'generatedAt' => new \DateTimeImmutable(),
        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="scada_devices_report.pdf"',
            ]
        );
    }
}