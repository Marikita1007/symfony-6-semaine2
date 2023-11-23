<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class CheckUserConnectionSubscriber implements EventSubscriberInterface
{

    public function __construct(private LoggerInterface $logger){}

    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        $this->logger->debug('Connection Test = OK');
        $token = $event->getAuthenticatedToken();
        $this->logger->info('The token : ' . $token);

        $user = $token->getUser();
        $email = $user->getEmail();
        $this->logger->info('Email : ' . $email);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccessEvent',
        ];
    }
}
