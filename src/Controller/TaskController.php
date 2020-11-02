<?php 
namespace App\Controller;

use App\Entity\Task;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController {
    protected function getUser()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        return $user;
    }
    /**
     * @Route("/task", name="task")
     */
    public function task() {
        $user = $this->getUser();
        $repository = $this->getDoctrine()->getRepository(Task::class);
        $tasks = $repository->findBy(['user' =>$user->getId()]);
        return $this->render('tasks/index.html.twig', array('tasks' => $tasks, 'user' => ['id' => $user->getId(), 'email' => $user->getEmail()]));
    }
    /**
     * @Route ("/task/new", name="new_task")
     * Method ({"GET", "POST"})
     */
    public function new(Request $request) {
        $task = new Task();
        $form = $this->createFormBuilder($task)
        ->add('body', TextareaType::class, array('required' => true, 
        'attr' => array('class' => 'form-control'), 'label' => 'Task'))
        ->add('save', SubmitType::class, array(
            'label' => 'Save Task',
            'attr' => array('class' => 'btn btn-primary mt-3')
        ))
        ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $task = $form->getData();
            $task->setUser($user);
            $task->setIsDone(false);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirectToRoute('task');
        }
        return $this->render('tasks/new.html.twig', array(
            'form' => $form->createView()
        ));
    }
    /**
     * @Route ("/task/{id}", name="task_show")
     * @Method ({"GET"})
     */
    public function show($id) {
        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);
        $isDone = $task->getIsDone();
        return $this->render('tasks/show.html.twig', array('task' => $task, 'isDone' => $isDone));
    }

    /**
     * @Route ("/task/update/{id}", name="update_task")
     * Method ({"GET", "POST"})
     */
    public function update(Request $request, $id) {
        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);
        $form = $this->createFormBuilder($task)
        ->add('body', TextareaType::class, array('required' => true, 
        'attr' => array('class' => 'form-control'), 'label' => 'Task'))
        ->add('isDone', CheckboxType::class, [
            'label'    => 'Is it finished?',
            'required' => false,
        ])
        ->add('save', SubmitType::class, array(
            'label' => 'Save Task',
            'attr' => array('class' => 'btn btn-primary mt-3')
        ))
        ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('task_show', array('id' => $id));
        }
        return $this->render('tasks/update.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @route("/task/delete/{id}")
     * @Method ({"DELETE"})
     */
    
    public function delete(Request $request, $id) {
        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($task);
        $entityManager->flush();
        return $this->redirectToRoute('task');
    }

}