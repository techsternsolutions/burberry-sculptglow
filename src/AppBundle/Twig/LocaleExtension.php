<?php
namespace AppBundle\Twig;

use JMS\DiExtraBundle\Annotation\InjectParams;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;

use Symfony\Component\Intl\Intl;
/**
 * This Twig extension takes the list of codes of the locales (languages)
 * enabled in the application and returns an array with the name of each
 * locale written in its own language (e.g. English, Français, Español, etc.)
 *
 * @Service(public=false, id="app.twig.locales")
 * @Tag("twig.extension")
 *
 */
class LocaleExtension extends \Twig_Extension
{
    private $locales;

    /**
     * @param $locales
     *
     * @InjectParams({
     *     "locales" = @Inject("%locales%")
     * })
     */
    public function __construct($locales)
    {
        $this->locales = $locales;
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('locales', array($this, 'getLocales')),
        );
    }
    public function getLocales()
    {
        $localeCodes = explode('|', $this->locales);
        $locales = [];
        foreach ($localeCodes as $localeCode) {
            $locales[] = [
                'code' => $localeCode,
                'name' => $localeCode == 'ara' ? 'Arabic' : Intl::getLocaleBundle()->getLocaleName($localeCode, $localeCode)
            ];
        }
        return $locales;
    }
    // the name of the Twig extension must be unique in the application
    public function getName()
    {
        return 'app.locale_extension';
    }
}
