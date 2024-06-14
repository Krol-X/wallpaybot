<?php

namespace App\Controller;

use App\Interface\Service\TelegramParseServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class BotWebhookController extends AbstractController
{
    public function __construct(
        private readonly TelegramParseServiceInterface $parser,
        private readonly MessageBusInterface $bus
    )
    {
    }

    #[Route('/api/v1/telegram/webhook', name: 'app_bot_webhook')]
    public function index(Request $request): Response
    {
        $updates_data = json_decode($request->getContent(), true);
        $events = $this->parser->parseUpdatesData($updates_data);
        foreach ($events as $event) {
            $event->send($this->bus);
        }
    }
}
