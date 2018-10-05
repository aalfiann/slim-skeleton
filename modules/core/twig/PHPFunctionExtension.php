<?php
namespace modules\core\twig;
class PHPFunctionExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'PHP Function in Twig Extension';
    }
    
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('ucwords', 'ucwords')
        ];
    }
    
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('ucwords', 'ucwords')
        ];
    }
}