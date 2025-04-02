<?php

namespace App\Controller;

use App\Form\SendFileType;
use App\Services\DownloadImageOrZip;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        DownloadImageOrZip $dl
    ): StreamedResponse | Response {

        $form = $this->createForm(SendFileType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {


                /**
                 * @var UploadedFile $file
                 */
                $files = $form->get('file')->getData();
                $message = $dl->downloadFiles($files);

                $this->addFlash('success', $message);
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('app_home');
            }
        }
        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
