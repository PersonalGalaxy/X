<?php
declare(strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\{
    HttpFoundation\Response,
};

final class HomeController extends Controller
{
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }
}
