<?php

declare(strict_types=1);

namespace App\Command;

use App\Persistence\CouchDB\Client as CouchDBClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixturesCommand extends Command
{
    protected static $defaultName = 'app:data-fixtures';
    protected CouchDBClient $couchDbClient;

    /**
     * FixturesCommand constructor.
     */
    public function __construct(CouchDBClient $couchDbClient)
    {
        $this->couchDbClient = $couchDbClient;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure():void
    {
        $this
            ->setDescription('Creates data fixtures.')
            ->setHelp('This command generates data fixtures.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->couchDbClient->hasDB('_users')) {
            $this->couchDbClient->createDB('_users');
        }

        return 0;
    }
}
