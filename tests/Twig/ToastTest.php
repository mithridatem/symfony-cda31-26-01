<?php

namespace App\Tests\Twig;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Twig\Environment;

class ToastTest extends KernelTestCase
{
    public function testItDisplaysAndConsumesTheSupportedFlashTypes(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $request = Request::create('/');
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);
        $container->get('request_stack')->push($request);

        $variants = [
            'notice' => ['bg-sky-50', 'status', 'Information de test'],
            'warning' => ['bg-amber-50', 'status', 'Avertissement de test'],
            'danger' => ['bg-red-50', 'alert', 'Erreur de test'],
            'success' => ['bg-emerald-50', 'status', 'Succès de test'],
        ];
        $flashBag = $session->getFlashBag();
        foreach ($variants as $type => [, , $message]) {
            $flashBag->add($type, $message);
        }
        $flashBag->add('other', 'Autre message');

        $template = $container->get(Environment::class)->createTemplate('<twig:Toast />');
        $html = new Crawler($template->render());

        foreach ($variants as [$class, $role, $message]) {
            $toast = $html->filter(sprintf('.%s[role="%s"]', $class, $role));
            self::assertCount(1, $toast);
            self::assertStringContainsString($message, $toast->text());
        }

        self::assertSame('', trim($template->render()));
        self::assertSame(['Autre message'], $flashBag->peek('other'));
    }
}
