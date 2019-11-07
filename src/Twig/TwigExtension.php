<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
//use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('diff_updated', [$this, 'diffUpdated']),
        ];
    }

    public function diffUpdated(\Datetime $date_created, \Datetime $date_updated)
    {
        $diff = $date_created->diff($date_updated);

        return $diff->format('%a');
    }
}