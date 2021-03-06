<?php
/**
 * This file is part of the RedKite CMS Application and it is distributed
 * under the MIT License. To use this application you must leave
 * intact this copyright notice.
 *
 * Copyright (c) RedKite Labs <webmaster@redkite-labs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.redkite-labs.com
 *
 * @license    MIT License
 *
 */

namespace RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Listener\Page;

use RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Event\Content\Page\BeforeAddPageCommitEvent;
use RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Repository\Factory\FactoryRepositoryInterface;

/**
 * Listen to the onBeforeAddPageCommit event to add the page's contents, when a new
 * page is added
 *
 * @author RedKite Labs <webmaster@redkite-labs.com>
 *
 * @api
 */
class AddPageBlocksListener
{
    private $languageRepository;

    /**
     * Constructor
     *
     * @param FactoryRepositoryInterface $factoryRepository
     *
     * @api
     */
    public function __construct(FactoryRepositoryInterface $factoryRepository)
    {
        $this->languageRepository = $factoryRepository->createRepository('Language');
    }

    /**
     * Adds the contents for the page when a new page is added, for each language of the site
     *
     * @param  \RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Event\Content\Page\BeforeAddPageCommitEvent $event
     * @return boolean
     * @throws \Exception
     *
     * @api
     */
    public function onBeforeAddPageCommit(BeforeAddPageCommitEvent $event)
    {
        if ($event->isAborted()) {
            return;
        }

        $pageManager = $event->getContentManager();
        $templateManager = $pageManager->getTemplateManager();
        $pageRepository = $pageManager->getPageRepository();

        try {
            $languages = $this->languageRepository->activeLanguages();
            if (count($languages) > 0) {
                $result = true;
                $templateManager->getBlockRepository()->setConnection($pageRepository->getConnection());
                $pageRepository->startTransaction();
                // The min number of pages is setted to 1 because we are adding a page which has been saved but not
                // committed so it counts as one
                $ignoreRepeatedSlots = $pageManager->getValidator()->hasPages(1);
                $idPage = $pageManager->get()->getId();
                foreach ($languages as $alLanguage) {
                    $result = $templateManager->populate($alLanguage->getId(), $idPage, $ignoreRepeatedSlots);

                    if ($result === false) break;
                }

                if ($result === false) {
                    $pageRepository->rollBack();

                    $event->abort();
                } else {
                    $pageRepository->commit();
                }
            }
        } catch (\Exception $e) {
            $event->abort();
            if (isset($pageRepository) && $pageRepository !== null) {
                $pageRepository->rollBack();
            }

            throw $e;
        }
    }
}
