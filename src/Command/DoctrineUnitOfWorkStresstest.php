<?php
declare(strict_types=1);

namespace App\Command;

use App\DataFixtures\AppFixtures;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(name: 'app:doctrine-unit-of-work')]
class DoctrineUnitOfWorkStresstest extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Stopwatch $stopwatch,
        private readonly PostRepository $postRepository,
        private readonly TagRepository $tagRepository,
        private readonly AppFixtures $appFixtures,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->entityManager->getConnection()->createQueryBuilder()
            ->delete('symfony_demo_comment')
            ->executeStatement();
        $this->entityManager->getConnection()->createQueryBuilder()
            ->delete('symfony_demo_post')
            ->executeStatement();
        $this->entityManager->getConnection()->createQueryBuilder()
            ->delete('symfony_demo_tag')
            ->executeStatement();
        $this->entityManager->getConnection()->createQueryBuilder()
            ->delete('symfony_demo_user')
            ->executeStatement();

        $this->appFixtures->setReferenceRepository(new ReferenceRepository($this->entityManager));
        $this->appFixtures->load($this->entityManager);
        var_dump($this->entityManager->getConfiguration()->getSQLLogger());
        die;

        $this->stopwatch->start('benchmark');

        $output->writeln(\sprintf('Lap: %s', $this->stopwatch->lap('benchmark')));

        for ($i = 0; $i < 1000; ++$i) {
            $tag = $this->tagRepository->findOneBy(['name' => 'lorem']);
            $posts = $this->postRepository->findLatest(1, $tag);

            /** @var Post $post */
            foreach ($posts->getResults() as $post) {
                $object = new User();
                $object->setEmail('flo@gmx.de' . \rand());
                $object->setUsername($post->getAuthor()?->getUsername() . \rand());
                $object->setFullName('');
                $object->setPassword('');

                $this->entityManager->persist($object);

                $tag = new Tag($post->getSlug() . \rand() ?? 'hello-there');
                $this->entityManager->persist($tag);
            }

            $this->entityManager->flush();
            // $this->entityManager->clear();

            if ($i % 100 === 0) {
                $output->writeln(\sprintf('Lap: %s', $this->stopwatch->lap('benchmark')));
            }
        }

        $output->writeln((string) $this->stopwatch->stop('benchmark'));

        return 0;
    }

}