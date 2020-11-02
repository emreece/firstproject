<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;        
    }

    /**
     * @Route("/register", name="register")
     */
    public function index(Request $request,UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createFormBuilder($user)
        ->add('email', TextType::class, array('attr' =>array('class' => 'form-control')))
        ->add('plainPassword', RepeatedType::class, array(
            'type'              => PasswordType::class,
            'mapped'            => false,
            'first_options'     => array('label' => 'New password'),
            'second_options'    => array('label' => 'Confirm new password'),
            'invalid_message' => 'The password fields must match.',
        ))
        ->add('save', SubmitType::class, array(
            'label' =>'Create',
            'attr' =>array('class'=>'btn btn-primary mt-3')
        ))
        ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
        $user = $form->getData();
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('index');
        }
        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
            'form'=>$form->createView()
        ]);
    }
}
