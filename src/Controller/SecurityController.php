<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\RegistrationType;
use App\Form\EditProfilType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Service\Message;
use App\Service\MessagePassword;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, Message $message, \Swift_Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $fileName =  md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('images_directory'), $fileName);
            }
            $user->setimage($fileName);
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setToken(md5(uniqid()));
            $manager->persist($user);
            $manager->flush();
            $createMessage = $message->createMessage($user);
            $mailer->send($createMessage);
            $this->addFlash('success', 'Un email de confirmation vous a été envoyé. Pour activer votre compte, cliquez sur le lien dans le mail.');
            return $this->redirectToRoute('home');
        }
        return $this->render('security/registration.html.twig', [
            'formUser' => $form->createView()
        ]);
    }

    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, EntityManagerInterface $manager, UserRepository $userRepo)
    {
        $user = $userRepo->findOneBy(['token' => $token]);
        if (!$user) {
            throw $this->createNotFoundException('cet utilisateur n\'existe pas');
        }
        $user->setToken(null);
        $manager->persist($user);
        $manager->flush();
        $this->addFlash('success', 'Votre compte a bien été activé.');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("{id}/editProfil", name="editProfil")
     */
    public function editProfil(User $user, Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $userAuthentified = $this->getUser()->getId();
        if ($userAuthentified == $user->getId()) {
            $form = $this->createForm(EditProfilType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $image = $form->get('image')->getData();
                if (!empty($image)) {
                    if (!empty($user->getImage())) {
                        $name = $user->getImage();
                        unlink($this->getParameter('images_directory') . '/' . $name);
                    }
                    $file = md5(uniqid()) . '.' . $image->guessExtension();
                    $image->move($this->getParameter('images_directory'), $file);
                    $user->setimage($file);
                }
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', 'vos données ont bien été modifiées');
                return $this->redirectToRoute('home');
            }
        } else {
            return $this->redirectToRoute('home');
        }
        return $this->render('security/editProfil.html.twig', ['formUser' => $form->createView()]);
    }

    /**
     * @Route("/mot-de-passe-oublie", name="forgotten_password")
     */
    public function forgotPassword(Request $request, UserRepository $userRepo, \Swift_Mailer $mailer, MessagePassword $messagePassword, TokenGeneratorInterface $token, EntityManagerInterface $manager)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $userRepo->findOneByMail($data['mail']);
            if (!$user) {
                $this->addFlash('warning', 'cette adresse n\'existe pas');
                return  $this->redirectToRoute('forgotten_password');
            }
            $theToken = $token->generateToken();
            try {
                $user->setResetToken($theToken);
                $manager->persist($user);
                $manager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', "une erreur est survenue");
                return  $this->redirectToRoute('app_login');
            }
            $message = $messagePassword->createMessagePassword($user, $theToken);
            $mailer->send($message);
            $this->addFlash('success', 'Un email de reinitialisation de mot de passe vous a été envoyé');
            return  $this->redirectToRoute('app_login');
        }
        return $this->render('security/forgottenPassword.html.twig', ['emailForm' => $form->createView()]);
    }

    /**
     * @Route("/reset_password/{token}",name="app_reset_password")
     */
    public function resetPassword($token, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['resetToken' => $token]);
        if (!$user) {
            $this->addFlash('warning', 'Token inconnu');
            return $this->redirectToRoute(('app_login'));
        }
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user,$password));
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Votre mot de passe a été modifié.');
            return $this->redirectToRoute('app_login');
        } else {
            return $this->render(
                'security/resetPassword.html.twig',
                ['token' => $token, 'formUser' => $form->createView()]
            );
        }
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
