<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\Core\Twig\Extension;

use Shopware\Core\Framework\Log\Package;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

#[Package('core')]
class GroupMenuItems extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('groupAccountMenuItems', $this->groupAccountMenuItems(...)),
        ];
    }

    public function groupAccountMenuItems(array $menu): ?array
    {
        foreach ($menu as $key => $item) {
            if (isset($item['group'])) {
                if (!isset($item['position'])) {
                    $menu[$key]['position'] = 0;
                }
                if (isset($menu[$item['group']])) {
                    $menu[$item['group']]['submenu'][$key] = $item;
                    unset($menu[$key]);
                } else {
                    // move menu item to bottom if his group does not exist
                    $menu[$key]['position'] += 1000;
                }
            }
        }
        \uasort($menu, fn ($a, $b) => $a['position'] <=> $b['position']);

        return $menu;
    }
}
