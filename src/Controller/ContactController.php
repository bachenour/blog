<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use App\Form\ContactType;



class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $contactFormData = $form->getData();
            
            $message = (new Email)
                ->from($contactFormData['email'])
                ->to('bache.nour2@gmail.com')
                ->subject('Demande de contact : Nouveau message de contact')
                ->text('Sender : '.$contactFormData['email'].\PHP_EOL.
                    $contactFormData['message'],
                    'text/plain')
                    ->html('<h1>Nouveau message de contact</h1>')
                    ->html('<p>Message de : '.$contactFormData['email'].'</p>')
                    ->html('<p>'.$contactFormData['message'].'</p>');
            $mailer->send($message);

            $this->addFlash('success', 'message a été envoyé');

            return $this->redirectToRoute('articles');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
