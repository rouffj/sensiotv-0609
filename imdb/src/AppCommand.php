<?php

namespace App;

use App\Omdb\OmdbClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class AppCommand extends Command
{
    public function __construct(OmdbClient $api)
    {
        parent::__construct('app');
        $this->api = $api;
    }

    protected function configure()
    {
        $this
            ->setDescription('@TODO Add a well documented description of what this command is doing.')
            ->addArgument('keyword', InputArgument::OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $keyword = $input->getArgument('keyword');

        if (!$keyword) {
            $keyword = $io->ask('Which movie you are looking for?', 'Harry Potter', [$this, 'checkInput']);
        } else {
            $this->checkInput($keyword);
        }

        $io->title('You are looking for movies containing "'.$keyword.'"');

        $search = $this->api->requestBySearch($keyword, ['type' => 'movie']);

        $io->success($search['totalResults'] . ' movies match your request');

        $rows = [];
        $io->progressStart(count($search['Search']));
        foreach($search['Search'] as $movie) {
            usleep(100000);
            $io->progressAdvance();
            $rows[] = [$movie['Title'], $movie['Year'], 'https://www.imdb.com/title/'.$movie['imdbID'].'/', '<href='.$movie['Poster'].'>Preview</>'];
        }
        $output->write("\r"); // allow to remove the progress bar once completed.

        $io->table(['TITLE', 'YEAR', 'URL', 'PREVIEW'], $rows);

        //var_dump($search);die;


        $io->success('Hello from imdb command :)!');

        return 0;
    }

    public function checkInput($answer) {
        $answer = strtolower($answer);
        $forbiddenKeywords = ['hassle', 'shit', 'fuck'];

        foreach($forbiddenKeywords as $keyword) {
            if (false !== strpos($answer, $keyword)) {
                throw new \InvalidArgumentException('Your keyword is not valid, please try an other one ;)');
            }
        }

        return $answer;
    }
}

