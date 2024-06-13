<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FooController extends AbstractController
{
    #[Route('/foo', name: 'app_foo')]
    public function index(LoggerInterface $logger): Response
    {
        $logger->info('Test');
        return $this->json(["status" => "ok"]);
    }
}
