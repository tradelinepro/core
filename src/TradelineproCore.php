<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\Core;

use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Plugin;

#[Package('core')]
class TradelineproCore extends Plugin
{
    public function getTemplatePriority(): int
    {
        // set priority to allow other plugins change some templates,
        // defined in this plugin
        // other plugins should have the value > than here
        return 10;
    }
}
