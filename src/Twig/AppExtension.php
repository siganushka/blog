<?php

namespace App\Twig;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('human_date', [$this, 'humanDate'], ['is_safe' => ['html']]),
            new TwigFunction('linked_username', [$this, 'linkedUsername'], ['is_safe' => ['html']]),
        ];
    }

    public function humanDate(\DateTimeInterface $dateTime, string $format = 'm/d H:i')
    {
        return sprintf('<em title="%s">%s</em>',
            $dateTime->format('Y/m/d H:i:s'),
            $dateTime->format($format));
    }

    public function linkedUsername(?User $user)
    {
        if ($user && $user->getUrl()) {
            return sprintf('<a href="%s" target="_blank">%s</a>', $user->getUrl(), $user->getUsername());
        }

        return sprintf('<span>%s</span>', $user->getUsername());
    }
}
