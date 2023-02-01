<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

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
     * Simple mercure demo endpoint.
     * Publishes the sent message to every subscriber.
     *
     * @param Request $request
     * @param HubInterface $hub
     * @return JsonResponse
     */
    #[Route('/publishMsg', name: 'publishMsg', methods: ['POST'])]
    public function publishMsg(Request $request, HubInterface $hub): JsonResponse
    {
        try {
            // Validate request body
            $data = json_decode($request->getContent());
            $msg = (is_object($data) && isset($data->message)) ? $data->message : null;

            if (!$msg) {
                throw new \Exception('message property is missing!');
            }

            // Publish update
            $update = new Update(
                'https://localhost/subscribeMsg',
                json_encode([
                    'message' => $msg
                ])
            );

            $hub->publish($update);

            // Response
            return $this->json([
                'success' => true,
                'message' => 'Message published!'
            ]);
        } catch (\Exception $err) {
            return $this->json([
                'success' => false,
                'error' => $err->getMessage()
            ]);
        }
    }
}
