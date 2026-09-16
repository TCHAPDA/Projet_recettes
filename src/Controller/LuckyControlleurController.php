<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class LuckyControlleurController extends AbstractController
{
    #[Route('/lucky/controlleur', name: 'app_lucky_controlleur')]
    public function index(): Response
    {
        return $this->render('lucky_controlleur/index.html.twig', [
            'controller_name' => 'LuckyControlleurController',
        ]);
    }
//mission 2
    #[Route('/lucky/number', name: 'app_lucky_number')]
    public function show_number(): Response
    {
    $number = random_int(0, 100);
    
    return new Response('Lucky number: ' .$number);
    }
//mission3
    #[Route('/lucky/number_for_username', name: 'app_lucky_number_for_username')]
    public function show_number_v2(Request $request): Response
    {
        //on recupere le parametre GET ?username=..
        $username = $request->query->get('username', 'Anonyme');
        $number = random_int(0 ,100);

        return new Response('Nombre tiré au sort : '.$number.' pour '.$username);
    }
//mission4
    #[Route('/lucky/number_v3', name: 'app_lucky_number_v3')]
    public function show_number_v3(Request $request): Response
    {
        //on genere un tableau de 10 tirage au sort
        $number =[];
        for ($i = 0; $i < 10; $i++) {
            $number[] = random_int(0, 100);
        }
        return $this->render('lucky_controlleur/number.html.twig', [
            'numbers' => $number,
        ]);
    }
}