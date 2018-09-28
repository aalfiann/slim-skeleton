<?php
namespace modules\core\twig;
class GlobalTwigVariable extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{

    /**
     * @var globalvariable is data array
     */
    protected $globalvariable;
    
    public function __construct(array $globalvariable)
    {
        $this->globalvariable = $globalvariable;
    }

    public function getGlobals()
    {
        if (!empty($this->globalvariable)) return $this->globalvariable;
        return [];
    }

    public function getName()
    {
        return 'Global Twig Variable Extension';
    }
}