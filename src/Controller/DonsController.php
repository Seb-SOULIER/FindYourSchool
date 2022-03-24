<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Donate;
use App\Form\CommentType;
use App\Form\DonateType;
use App\Repository\CommentRepository;
use App\Repository\DonateRepository;
use App\Service\SendMail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DonsController extends AbstractController
{
    /**
     * @Route("/dons", name="app_dons")
     */
    public function index(Request $request, CommentRepository $commentRepository,DonateRepository $donateRepository, SendMail $sendMail): Response
    {
        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);

        $donate = new Donate();
        $formDonate = $this->createForm(DonateType::class, $donate);
        $formDonate->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $commentRepository->add($comment);
            $send = $sendMail->mailComment($formComment['mail']->getData(),$formComment['comment']->getData());
            if($send==="ok"){
                $this->addFlash('success','Message envoyé');
                return $this->redirectToRoute('app_student', [], Response::HTTP_SEE_OTHER);
            }else{
                $this->addFlash('danger','Message non envoyé');
            }
        }

        if ($formDonate->isSubmitted() && $formDonate->isValid()) {
            $donateRepository->add($donate);
        }

        return $this->renderForm('dons/index.html.twig', [
            'formComment' => $formComment,
            'formDonate' => $formDonate,
            'donate'=>$donate
        ]);
    }

    /**
     * @Route("/stripe/create/session/{donate}", name="stripe_create_session", methods={"POST"})
     */
    public function stripeCreateSession(Request $request,Donate $donate)
    {
        if($_ENV['APP_ENV']==='dev'){
            $stripeKey=$_ENV['STRIPE_SECRET_KEY_TEST'];
        }else{
            $stripeKey=$_ENV['STRIPE_SECRET_KEY_LIVE'];
        }

        \Stripe\Stripe::setApiKey($stripeKey);

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $donate->getAmount()*100,
                    'product_data' => [
                        'name' => 'Don pour le site \'Trouve ton école\'',
                    ],
                ],
                'description'=>'Don pour l\'entreprise \'COD4Y\', pour le maintien du fonctionnement, l\'entretien technique, la mise à jour du site \'Trouve ton école\'. Aussi pour continuer a vous épargner le moindre bandeau publicitaire. Nous vous remercions pour ce don de : '.$donate->getAmount().' €uros',
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('valid_payment', ['donate'=>$donate->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cart_canceled', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'customer_email' => $donate->getMail(),
        ]);

        return new JsonResponse(['id' => $checkout_session->id]);
    }

    /**
     * @Route("/commande/annulation/", name="cart_canceled")
     */
    public function cartCancel()
    {
        $this->addFlash('danger','Don non effectué');
        return $this->redirectToRoute('app_dons', []);
    }

    /**
     * @Route("/commande/validation/{donate}", name="valid_payment")
     */
    public function cartSuccess(Donate $donate, SendMail $sendMail)
    {
        $this->addFlash('success','Merci pour votre don');
        $sendMail->mailDonate($donate->getMail(),$donate->getAmount());
        return $this->redirectToRoute('app_student', []);
    }
}
