<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExpenseController extends Controller
{
    /**
     * @Route("/expense", name="expense")
     */
    public function index()
    {
        return $this->render('expense/index.html.twig', [
            'controller_name' => 'ExpenseController',
        ]);
    }
}
