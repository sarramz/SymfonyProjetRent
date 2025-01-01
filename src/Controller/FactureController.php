<?php

namespace App\Controller;

use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Facture;
use Dompdf\Options;
use Dompdf\Dompdf;

class FactureController extends AbstractController
{
    #[Route('/facture/{id}/download', name: 'app_facture_download', methods: ['GET'])]
    public function download(FactureRepository $factureRepository, $id): Response
    {
        // Récupérer la facture depuis le repository
        $facture = $factureRepository->find($id);

        if (!$facture) {
            throw $this->createNotFoundException('Facture non trouvée.');
        }

        // Configure Dompdf selon vos besoins
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        // Générer le contenu du PDF
        $html = $this->renderView('facture/pdf.html.twig', [
            'facture' => $facture,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Retourner le PDF sous forme de réponse
        $filename = 'facture-' . $facture->getId() . '.pdf';
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
