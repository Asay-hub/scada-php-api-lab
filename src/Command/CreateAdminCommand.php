<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:admin:create',
    description: 'Створює адміністратора (ROLE_ADMIN) із прихованим введенням пароля'
)]
final class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'Email адміністратора')
            ->addArgument('password', InputArgument::OPTIONAL, 'Пароль адміністратора');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email') ?? $io->ask('Email адміністратора');
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $io->error('Некоректний email.');
            return Command::FAILURE;
        }

        $existing = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existing) {
            $io->error("Користувач {$email} уже існує.");
            return Command::FAILURE;
        }

        $plainPassword = $input->getArgument('password') ?? $io->askHidden('Пароль (не відображається при введенні)');
        if (!$plainPassword || strlen($plainPassword) < 6) {
            $io->error('Пароль має містити щонайменше 6 символів.');
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        $this->em->persist($user);
        $this->em->flush();

        $io->success("Адміністратора {$email} успішно створено!");
        return Command::SUCCESS;
    }
}