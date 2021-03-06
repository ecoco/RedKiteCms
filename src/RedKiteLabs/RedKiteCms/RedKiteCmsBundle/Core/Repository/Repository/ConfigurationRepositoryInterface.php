<?php
/**
 * This file is part of the RedKite CMS Application and it is distributed
 * under the MIT License. To use this application you must leave
 * intact this copyright notice.
 *
 * Copyright (c) RedKite Labs <webmaster@redkite-labs.com>
 *
 * For the full copyright and license infpageRepositoryation, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.redkite-labs.com
 *
 * @license    MIT License
 *
 */

namespace RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Repository\Repository;

use RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Model\Configuration;

/**
 * Defines the methods used to fetch configuration records
 *
 * @author RedKite Labs <webmaster@redkite-labs.com>
 */
interface ConfigurationRepositoryInterface
{
    /**
     * Fetches the given parameter
     *
     * @param  string          $parameter The parameter name
     * @return Configuration The configuration instance
     */
    public function fetchParameter($parameter);
}
