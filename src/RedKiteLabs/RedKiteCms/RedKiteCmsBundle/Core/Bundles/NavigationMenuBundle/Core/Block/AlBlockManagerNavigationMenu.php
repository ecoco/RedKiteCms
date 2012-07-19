<?php
/*
 * This file is part of the AlphaLemon CMS Application and it is distributed
 * under the GPL LICENSE Version 2.0. To use this application you must leave
 * intact this copyright notice.
 *
 * Copyright (c) AlphaLemon <webmaster@alphalemon.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.alphalemon.com
 *
 * @license    GPL LICENSE Version 2.0
 *
 */

namespace AlphaLemon\AlphaLemonCmsBundle\Core\Bundles\NavigationMenuBundle\Core\Block;

use AlphaLemon\AlphaLemonCmsBundle\Core\Content\Block\AlBlockManager;
use AlphaLemon\AlphaLemonCmsBundle\Model\AlPageAttributePeer;
use AlphaLemon\AlphaLemonCmsBundle\Core\Repository\Repository\LanguageRepositoryInterface;
use AlphaLemon\AlphaLemonCmsBundle\Core\Repository\Factory\AlFactoryRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * AlBlockManagerMenu
 *
 * @author alphalemon <webmaster@alphalemon.com>
 */
class AlBlockManagerNavigationMenu extends AlBlockManager
{
    private $container = null;

    public function __construct(ContainerInterface $container, AlParametersValidatorInterface $validator = null)
    {
        $this->container = $container;
        $dispatcher = $container->get('event_dispatcher');
        $factoryRepository = $container->get('alphalemon_cms.factory_repository');
        parent::__construct($dispatcher, $factoryRepository, $validator);

        $this->languageRepository = $this->factoryRepository->createRepository('Language');
    }

    /**
     *  {@inheritdoc}
     */
    public function getDefaultValue()
    {
        return array("HtmlContent" => "<ul><li>En</li></ul>");
    }

    /**
     *  {@inheritdoc}
     */
    public function getHtmlContentForDeploy()
    {
        $content = '';
        $pageName = $this->container->get('al_page_tree')->getAlPage()->getPageName();
        $router = $this->container->get('router');
        $languages = $this->languageRepository->activeLanguages();
        foreach($languages as $language)
        {
            try
            {
                $languageName = $language->getLanguage();
                $route = sprintf('_%s_%s', $language, str_replace('-', '_', $pageName));
                $url = $router->generate($route);
            }
            catch(RouteNotFoundException $ex)
            {
                $url = "#";
                $languageName .= "[Error]";
            }

            $content .= sprintf('<li><a href="%s">%s</a></li>', $url, $languageName);
        }

        return sprintf('<ul>%s</ul>', $content);
    }

    /**
     *  {@inheritdoc}
     */
    public function getHtmlContent()
    {
        $content = '';
        $pageName = $this->container->get('al_page_tree')->getAlPage()->getPageName();
        $urlManager = $this->container->get('alphalemon_cms.url_manager');

        $languages = $this->languageRepository->activeLanguages();
        foreach($languages as $language)
        {
            $languageName = $language->getLanguage();
            $url = $urlManager
                    ->buildInternalUrl($languageName, $pageName)
                    ->getInternalUrl();
            if (null === $url) {
                $url = '#';
            }
            
            $content .= sprintf('<li><a href="%s">%s</a></li>', $url, $languageName);
        }

        return sprintf('<ul>%s</ul>', $content);
    }
}
