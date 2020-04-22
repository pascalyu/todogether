<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskNewType;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="task_index", methods={"GET","POST"})
     */
    public function index(Request $request, TaskRepository $taskRepository): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    /**
     * @Route("/new", name="task_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskNewType::class, $task, ['action' => $this->generateUrl('task_new')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $task->setUser($this->getUser());
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_show", methods={"GET"})
     */
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Task $task): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($task);
        $entityManager->flush();
        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->request->get('_token'))) {
        }

        return $this->redirectToRoute('task_index');
    }
}
