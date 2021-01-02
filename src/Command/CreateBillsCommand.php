<?php

namespace App\Command;

use App\Services\BillCreatorService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CreateBillsCommand extends Command
{
    protected static $defaultName = 'app:create:bills';

    protected BillCreatorService $billsCreator;

    protected HttpClientInterface $client;

    protected string $botKey;

    public function __construct(BillCreatorService $service, HttpClientInterface $client, string $botKey)
    {
        parent::__construct();
        $this->billsCreator = $service;
        $this->client = $client;
        $this->botKey = $botKey;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $this->billsCreator->execute();
        $message = $this->billsCreator->getTextMessage();
        $this->sendMessage($message, '263800667');

        $io->success('OK');

        return 0;
    }

    /**
     * @param string $message
     * @param string $chatId
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendMessage(string $message, string $chatId): bool
    {
        $this->client->request('GET',
            'https://api.telegram.org/bot'.
            $this->botKey.'/sendMessage?parse_mode=HTML&text='.
            urlencode($message).'&chat_id='.$chatId
        );

        return true;
    }
}
