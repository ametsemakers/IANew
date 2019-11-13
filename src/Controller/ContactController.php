<?php
namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();
            $message = (new \Swift_Message('Prise de contact l\'Instant Ayurveda'))
                ->setFrom($contactFormData['email'])
                ->setTo('email@domain.nettt')
                ->setBody(
                    $contactFormData['contenu'],
                    'text/plain'
                )
            ;
            $mailer->send($message);
            $this->addFlash('success', 'Message was send');
            return $this->redirectToRoute('home');
        }
        return $this->render('main/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}