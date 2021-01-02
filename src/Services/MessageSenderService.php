<?php

namespace App\Services;

use App\Repository\ExchangeRateRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MessageSenderService
{
    private string $botKey;
    private ServiceEntityRepositoryInterface $repository;
    private ServiceEntityRepositoryInterface $ratesRepo;
    private HttpClientInterface $client;
    private string $chatId = '';
    private string $message = '';

    public function __construct(
        SubscriptionRepository $repository,
        ExchangeRateRepository $repo,
        HttpClientInterface $client,
        string $botKey
    )
    {
        $this->repository = $repository;
        $this->ratesRepo = $repo;
        $this->botKey = $botKey;
        $this->chatId = getenv('MESSAGE_RECEIVER_CHAT_ID');
        $this->client = $client;
    }

    public function execute()
    {
        if (!$this->message || !$this->chatId) {
            throw new \Exception('message body or chat id not given');
        }

        $this->sendMessage($this->message, $this->chatId);
    }

    /**
     * @param string $message
     * @param string $chatId
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function sendMessage(string $message, string $chatId = null): void
    {
        if (!$chatId) {
            $chatId = $this->chatId;
        }
        $messageBody = 'https://api.telegram.org/bot' . $this->botKey . '/sendMessage?parse_mode=HTML&text=' . urlencode($message) . '&chat_id=' . $chatId;
        $this->client->request(Request::METHOD_GET, $messageBody);
    }
}
