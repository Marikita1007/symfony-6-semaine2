<?php

namespace App\Services;

use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageService
{
    public function showMessageService()
    {
        $messages = [
            "This is a message",
            "This is a second message",
            "This is a third message",
            "This is a fourth message"
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }
}