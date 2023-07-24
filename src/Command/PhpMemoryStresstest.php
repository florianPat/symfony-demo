<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(name: 'app:php-test')]
class PhpMemoryStresstest extends Command
{

    public function __construct(
        private readonly Stopwatch $stopwatch,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->stopwatch->start('benchmark');

        for ($i = 0; $i < 100000; ++$i) {
            $output->write('');
            $object = new User();
            $object->setEmail("flo@gmx.de");
            $other = new \stdClass();

            if ($i % 10000 === 0) {
                $output->writeln(\sprintf('Lap: %s', $this->stopwatch->lap('benchmark')));
            }
        }

        $output->writeln((string) $this->stopwatch->stop('benchmark'));

        return 0;
    }

}
