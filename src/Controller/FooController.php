<?php

namespace App\Controller;

use App\Message\FooMessage;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class FooController extends AbstractController
{
    #[Route('/foo', name: 'app_foo')]
    public function index(
        LoggerInterface     $logger,
        MessageBusInterface $bus
    ): Response
    {
        $logger->info('Test');
        $bus->dispatch(new FooMessage('Test with delay'));
        return $this->json(["status" => "ok"]);
    }
}
