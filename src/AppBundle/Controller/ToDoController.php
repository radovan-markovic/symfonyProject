<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ToDo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;




class ToDoController extends Controller
{
    /**
     * @Route("/", name="todo_list")
     */
    public function listAction(Request $request)
    {

        $todos = $this->getDoctrine()
                ->getRepository('AppBundle:ToDo')
                ->findAll();
      
        return $this->render('todo/index.html.twig', array ('todos' => $todos));
    }

    /**
     * @Route("/todos/create", name="todo_create")
     */
    public function createAction(Request $request)
    {
        $todo = new ToDo;

        $form = $this->createFormBuilder($todo)
        ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
        ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
        ->add('priority', ChoiceType::class, array('choices'=>array( 'Low'=>'Low', 'Normal'=>'Normal', 'High' => 'High') ,'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
        ->add('due_date', DateTimeType::class, array('attr' => array('style' => 'margin-bottom:15px')))
        ->add('submit', SubmitType::class, array('label'=>'Create ToDo', 'attr' => array('class' => 'btn btn-success', 'style' => 'margin-bottom:15px')))
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            
            $name = $form['name']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $due_date = $form['due_date']->getData();
            $now = new\DateTime('now');

            $todo->setName($name);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($due_date);
            $todo->setCreateDate($now);

            $em = $this->getDoctrine()->getManager();            
            $em->persist($todo);
            $em->flush();

            return $this->redirectToRoute('todo_list');

        }

        return $this->render('todo/create.html.twig', array ('form'=> $form->createView()));
    }

    /**
     * @Route("/todos/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:ToDo')
            ->find($id);

            $todo->setName($todo->getName());
            $todo->setDescription($todo->getDescription());
            $todo->setPriority($todo->getPriority());
            $todo->setDueDate($todo->getDueDate());
            $todo->setCreateDate($todo->getCreateDate());

            $form = $this->createFormBuilder($todo)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('priority', ChoiceType::class, array('choices'=>array( 'Low'=>'Low', 'Normal'=>'Normal', 'High' => 'High') ,'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('due_date', DateTimeType::class, array('attr' => array('style' => 'margin-bottom:15px')))
            ->add('submit', SubmitType::class, array('label'=>'Update ToDo', 'attr' => array('class' => 'btn btn-success', 'style' => 'margin-bottom:15px')))
            ->getForm();
    
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()){
                
                $name = $form['name']->getData();
                $description = $form['description']->getData();
                $priority = $form['priority']->getData();
                $due_date = $form['due_date']->getData();
                $now = new\DateTime('now');

                $em = $this->getDoctrine()->getManager();
                $todo = $this->getDoctrine()
                    ->getRepository('AppBundle:ToDo')
                    ->find($id);  
    
                $todo->setName($name);
                $todo->setDescription($description);
                $todo->setPriority($priority);
                $todo->setDueDate($due_date);
                $todo->setCreateDate($now);
               
                $em->flush();
    
                return $this->redirectToRoute('todo_list');
    
            }    

        return $this->render('todo/edit.html.twig', array('todo' => $todo, 'form' => $form->createView()));
    }

    /**
     * @Route("/todos/details/{id}", name="todo_details")
     */
    public function detailsAction($id)
    {
        return $this->render('todo/details.html.twig');
    }

    /**
     * @Route("/todos/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('AppBundle:ToDo') ->find($id);
        $em->remove($todo);
        $em->flush();
        
        return $this->redirectToRoute('todo_list');
    }
}
