<?php

namespace App\Command;

use App\Services\RatesFetcherService;
use App\Services\RatesFormatterService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Class RatesFetcherCommand
 * @package App\Command
 */
class RatesFetcherCommand extends Command
{
    protected static $defaultName = 'app:fetch:rates';

    private $botKey = '1092299871:AAGvl46y4bRpso-_u-wN2hSpkuNYkstb600';
    private $ratesFetcher;
    private $client;
    private $formatter;

    public function __construct(RatesFetcherService $service, RatesFormatterService $formatter)
    {
        parent::__construct();
        $this->ratesFetcher = $service;
        $this->formatter = $formatter;
        $this->client = HttpClient::create();
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

        $this->ratesFetcher->execute();
        $rates = $this->ratesFetcher->fetchOrGet();
        $this->sendMessage($this->formatter->formatForMessenger($rates), '263800667');

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
//        $chatId = isset($message['parsedObj']) ? $message['parsedObj']->getChat() : null;
//        if (!$chatId) { return false;}

        $this->client->request('GET',
            'https://api.telegram.org/bot'.
            $this->botKey.'/sendMessage?parse_mode=HTML&text='.
//                $this->botKey.'/sendMessage?text='.
            urlencode($message).'&chat_id='.$chatId
        );

        return true;
    }
}
