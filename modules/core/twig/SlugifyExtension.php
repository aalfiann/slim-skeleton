<?php
namespace modules\core\twig;
class SlugifyExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'Slugify Twig Extension';
    }
    
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('slugify', array($this, 'slugify'))
        ];
    }
    
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('slugify', array($this, 'slugify'))
        ];
    }

    public function slugify($string){
        return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
    }
}