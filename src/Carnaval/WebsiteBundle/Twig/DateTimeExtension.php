<?php
namespace Carnaval\WebsiteBundle\Twig;

class DateTimeExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'toDateString' => new \Twig_Filter_Method($this, 'toDateString'),
        );
    }

    public function toDateString($date, $format)
    {
        setlocale(LC_TIME, "fr_FR");
        return strftime($format,strtotime($date));
    }

    public function getName()
    {
        return 'datetime_extension';
    }
}

?>