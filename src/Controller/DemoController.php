<?php

namespace App\Controller;

use App\Entity\Message;
use Carbon\CarbonImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class DemoController extends AbstractController
{
    /**
     * Shows current php config.
     *
     * @return Response Phpinfo
     */
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): Response
    {
        return new Response(phpinfo());
    }

    /**
     * Simple Mercure and Doctrine demo endpoint.
     * Publishes a message to every subscriber.
     *
     * @param Request $request
     * @param HubInterface $hub
     * @return JsonResponse
     */
    #[Route('/messages', name: 'messagesPost', methods: ['POST'])]
    public function publishMsg(Request $request, ManagerRegistry $doctrine, HubInterface $hub): JsonResponse
    {
        $topicURL = $this->getParameter('mercure.topics');

        try {
            // Validate request body
            $data = json_decode($request->getContent());
            $msg = (is_object($data) && isset($data->message)) ? $data->message : null;

            if (!$msg) {
                throw new \Exception('message property is missing!');
            }

            // Save message to database
            $entityManager = $doctrine->getManager();

            $message = new Message();
            $message->setMessage($msg);
            $message->setPostedAt(new CarbonImmutable());

            $entityManager->persist($message);
            $entityManager->flush();

            // Publish update
            $update = new Update(
                $topicURL . '/messages',
                json_encode([
                    'id' => $message->getId(),
                    'message' => $message->getMessage(),
                    'postedAt' => $message->getPostedAt()
                ])
            );
            $hub->publish($update);

            // Response
            return $this->json([
                'success' => true,
                'message' => 'Message saved and published!'
            ]);
        } catch (\Exception $err) {
            return $this->json([
                'success' => false,
                'error' => [
                    'code' => $err->getCode(),
                    'message' => $err->getMessage()
                ]
            ]);
        }
    }

    /**
     * Simple Doctrine endpoint.
     * Get stored messages.
     *
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    #[Route('/messages', name: 'messagesGet', methods: ['GET'])]
    public function getMsgs(ManagerRegistry $doctrine, SerializerInterface $serializer): Response
    {
        try {
            // Get data
            $repository = $doctrine->getManager()->getRepository(Message::class);
            $messages = $repository->getStoredMsg();
            $data = [
                'success' => true,
                'messages' => $messages
            ];

            // Serialize data
            $data = $serializer->serialize($data, 'json', [ObjectNormalizer::class]);

            // Response
            $response = new Response($data);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } catch (\Exception $err) {
            return $this->json([
                'success' => false,
                'error' => [
                    'code' => $err->getCode(),
                    'message' => $err->getMessage()
                ]
            ]);
        }
    }
}
