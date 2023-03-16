<?php

namespace App\Controller;

use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;



class MessageVoteController extends AbstractController
{

    public function vote(Message $message, string $type): Response
    {
        if ($type !== 'upvote' && $type !== 'downvote') {
            throw $this->createNotFoundException();
        }



        if ($type === 'upvote') {
            $message->addUpvote();
        } else {
            $message->addDownvote();
        }


        return $this->json([
            'message' => 'Vote successful!',
        ]);
    }
}