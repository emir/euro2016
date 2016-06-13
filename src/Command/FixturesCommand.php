<?php

namespace Euro2016\Command;

use DateTime;
use DateTimeZone;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;

class FixturesCommand extends Command
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * FixturesCommand constructor.
     *
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct();

        $this->client = $client;
    }

    protected function configure()
    {
        $this
            ->setName('fixtures')
            ->setDescription('Fixtures')
            ->addArgument(
                'status',
                InputArgument::OPTIONAL,
                'TODAY, CURRENT, FINISHED, ALL are valid options. Default is today.'
            )
            ->addOption(
                'team',
                't',
                InputOption::VALUE_REQUIRED,
                'Only return specified team\'s matches'
            )
        ;
    }

    protected function fetch()
    {
        try{
            $request = $this->client->get('http://api.football-data.org/v1/soccerseasons/424/fixtures', [
                'headers' => [
                    'X-AUTH-TOKEN' => '53e6bee2dade46858d67b06f85972363'
                ]
            ]);
        } catch (\Exception $e) {
            return die("Looks like something wrong with API. You can always open issue here: https://github.com/emir/euro2016/issues\n");
        }

        return json_decode($request->getBody()->getContents());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $status = strtoupper($input->getArgument('status'));

        if(!$status) {
            $status = 'TODAY';
        }

        $data = $this->fetch();

        foreach ($data->fixtures as $fixture) {
            if($status == 'CURRENT' && $fixture->status == 'IN_PLAY') {
                $this->output($output, $fixture->status, $fixture->date, $fixture->homeTeamName, $fixture->awayTeamName, 
                    $fixture->result->goalsHomeTeam, $fixture->result->goalsAwayTeam);
            }

            if($status == 'ALL') {
                if ($input->getOption('team')) {
                    if($fixture->homeTeamName === $input->getOption('team') || $fixture->awayTeamName === $input->getOption('team')) {
                        $this->output($output, $fixture->status, $fixture->date, $fixture->homeTeamName, 
                            $fixture->awayTeamName, $fixture->result->goalsHomeTeam, $fixture->result->goalsAwayTeam);
                    }
                } else {
                    $this->output($output, $fixture->status, $fixture->date, $fixture->homeTeamName, 
                        $fixture->awayTeamName, $fixture->result->goalsHomeTeam, $fixture->result->goalsAwayTeam);
                }
            }

            if($status == 'FINISHED' && $fixture->status == 'FINISHED') {
                $this->output($output, $fixture->status, $fixture->date, $fixture->homeTeamName, $fixture->awayTeamName, 
                    $fixture->result->goalsHomeTeam, $fixture->result->goalsAwayTeam);
            }

            if($status == 'TODAY') {
                $match_date = new DateTime($fixture->date);
                $match_date->setTimezone(new DateTimeZone(date_default_timezone_get()));

                if($match_date->format('d') == (new DateTime())->format('d')) {
                    $this->output($output, $fixture->status, $fixture->date, $fixture->homeTeamName, 
                        $fixture->awayTeamName, $fixture->result->goalsHomeTeam, $fixture->result->goalsAwayTeam);
                }
            }
        }
    }

    protected function output(OutputInterface $output, $status, $date, $homeTeam, $awayTeam, $goalsHome, $goalsAway)
    {
        if(!$date instanceof DateTime) {
            $date = new DateTime($date);
            $date->setTimezone( new DateTimeZone(date_default_timezone_get()));
        }

        return $output->writeln("($status <comment>{$date->format('l M, d - H:i')}</comment>) <info>{$homeTeam} {$goalsHome} - {$awayTeam} {$goalsAway}</info>");
    }
}
