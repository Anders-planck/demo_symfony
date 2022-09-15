<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function Symfony\Component\String\u;


class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadPosts($manager);
    }

    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [ $password, $email, $roles]) {
            $user = new User();
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);

            $manager->persist($user);
            $this->addReference($email, $user);
        }

        $manager->flush();
    }


    private function loadPosts(ObjectManager $manager): void
    {
        $i =0;
        foreach ($this->getPostData() as [$title,  $content, $publishedAt, $author]) {
            $post = new Post();

            $post->setTitle($title)
                ->setDescription($content)
                ->setPosted(true)
                ->setCreatedAt($publishedAt);

            foreach (range(1, 5) as $i) {
                $comment = new Comment();
                $comment->setAuthor($this->getReference('john_user@symfony.com'));
                $comment->setContent($this->getRandomText(random_int(255, 512)));
                $comment->setPublishedAt(new DateTime('now + '.$i.'seconds'));

                $post->addComment($comment);
            }

            $manager->persist($post);
        }

        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            // $userData = [ $password, $email, $roles];
            [ 'kitten', 'jane_admin@symfony.com', ['ROLE_ADMIN']],
            [ 'kitten', 'tom_admin@symfony.com', ['ROLE_ADMIN']],
            [ 'kitten', 'john_user@symfony.com', ['ROLE_USER']],
        ];
    }

    private function getTagData(): array
    {
        return [
            'lorem',
            'ipsum',
            'consectetur',
            'adipiscing',
            'incididunt',
            'labore',
            'voluptate',
            'dolore',
            'pariatur',
        ];
    }

    private function getPostData(): array
    {
        $posts = [];
        foreach ($this->getPhrases() as $i => $title) {
            // $postData = [$title, $slug, $summary, $content, $publishedAt, $author, $tags, $comments];
            $posts[] = [
                $title,
                $this->getPostContent(),
                new \DateTimeImmutable('now - '.$i.'days'),
                // Ensure that the first post is written by Jane Doe to simplify tests
                $this->getReference(['jane_admin@symfony.com', 'tom_admin@symfony.com'][0 === $i ? 0 : random_int(0, 1)]),
            ];
        }

        return $posts;
    }

    private function getPhrases(): array
    {
        return [
            'Lorem ipsum dolor sit amet consectetur adipiscing elit',
            'Pellentesque vitae velit ex',
            'Mauris dapibus risus quis suscipit vulputate',
            'Eros diam egestas libero eu vulputate risus',
            'In hac habitasse platea dictumst',
            'Morbi tempus commodo mattis',
            'Ut suscipit posuere justo at vulputate',
            'Ut eleifend mauris et risus ultrices egestas',
            'Aliquam sodales odio id eleifend tristique',
            'Urna nisl sollicitudin id varius orci quam id turpis',
            'Nulla porta lobortis ligula vel egestas',
            'Curabitur aliquam euismod dolor non ornare',
            'Sed varius a risus eget aliquam',
            'Nunc viverra elit ac laoreet suscipit',
            'Pellentesque et sapien pulvinar consectetur',
            'Ubi est barbatus nix',
            'Abnobas sunt hilotaes de placidus vita',
            'Ubi est audax amicitia',
            'Eposs sunt solems de superbus fortis',
            'Vae humani generis',
            'Diatrias tolerare tanquam noster caesium',
            'Teres talis saepe tractare de camerarius flavum sensorem',
            'Silva de secundus galatae demitto quadra',
            'Sunt accentores vitare salvus flavum parses',
            'Potus sensim ad ferox abnoba',
            'Sunt seculaes transferre talis camerarius fluctuies',
            'Era brevis ratione est',
            'Sunt torquises imitari velox mirabilis medicinaes',
            'Mineralis persuadere omnes finises desiderium',
            'Bassus fatalis classiss virtualiter transferre de flavum',
        ];
    }

    private function getRandomText(int $maxLength = 255): string
    {
        $phrases = $this->getPhrases();
        shuffle($phrases);

        do {
            $text = u('. ')->join($phrases)->append('.');
            array_pop($phrases);
        } while ($text->length() > $maxLength);

        return $text;
    }

    private function getPostContent(): string
    {
        return <<<'MARKDOWN'
            Lorem ipsum dolor sit amet consectetur adipisicing elit, sed do eiusmod tempor
            incididunt ut labore et **dolore magna aliqua**: Duis aute irure dolor in
            reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
            deserunt mollit anim id est laborum.
              * Ut enim ad minim veniam
              * Quis nostrud exercitation *ullamco laboris*
              * Nisi ut aliquip ex ea commodo consequat
            Praesent id fermentum lorem. Ut est lorem, fringilla at accumsan nec, euismod at
            nunc. Aenean mattis sollicitudin mattis. Nullam pulvinar vestibulum bibendum.
            Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos
            himenaeos. Fusce nulla purus, gravida ac interdum ut, blandit eget ex. Duis a
            luctus dolor.
            Integer auctor massa maximus nulla scelerisque accumsan. *Aliquam ac malesuada*
            ex. Pellentesque tortor magna, vulputate eu vulputate ut, venenatis ac lectus.
            Praesent ut lacinia sem. Mauris a lectus eget felis mollis feugiat. Quisque
            efficitur, mi ut semper pulvinar, urna urna blandit massa, eget tincidunt augue
            nulla vitae est.
            Ut posuere aliquet tincidunt. Aliquam erat volutpat. **Class aptent taciti**
            sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi
            arcu orci, gravida eget aliquam eu, suscipit et ante. Morbi vulputate metus vel
            ipsum finibus, ut dapibus massa feugiat. Vestibulum vel lobortis libero. Sed
            tincidunt tellus et viverra scelerisque. Pellentesque tincidunt cursus felis.
            Sed in egestas erat.
            Aliquam pulvinar interdum massa, vel ullamcorper ante consectetur eu. Vestibulum
            lacinia ac enim vel placerat. Integer pulvinar magna nec dui malesuada, nec
            congue nisl dictum. Donec mollis nisl tortor, at congue erat consequat a. Nam
            tempus elit porta, blandit elit vel, viverra lorem. Sed sit amet tellus
            tincidunt, faucibus nisl in, aliquet libero.
            MARKDOWN;
    }
}
