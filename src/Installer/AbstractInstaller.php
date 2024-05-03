<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\Core\Installer;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\CountAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Metric\CountResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[Package('core')]
abstract class AbstractInstaller implements InstallerInterface
{
    public const REPOSITORY_DATA = [];

    public function __construct(
        protected ?ContainerInterface $container,
    ) {
        if (!\defined('static::REPOSITORY_DATA')) {
            throw new \Exception('Constant REPOSITORY_DATA is not defined on subclass ' . static::class);
        }
    }

    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    public function activate(ActivateContext $context): void
    {
        if (!$this->getContainer()) {
            return;
        }
        $context = $context->getContext();
        $repositories = static::REPOSITORY_DATA + $this->getRepositoryData();

        foreach ($repositories as $repository => $data) {
            if ($this->getContainer() && $this->getContainer()->has($repository)) {
                $this->insertIgnore($this->getContainer()->get($repository), $data, $context);
            }
        }
    }

    protected function insertIgnore(?object $container, array $data, Context $context): void
    {
        if (!$container instanceof EntityRepository) {
            throw new \Exception('REPOSITORY_DATA has an error, entity repository not found for ' . static::class);
        }
        foreach ($data as $entityId => &$item) {
            $criteria = (new Criteria([$entityId]))
                ->addAggregation(new CountAggregation('count', 'id'))
                ->setLimit(1);

            /** @var CountResult $count */
            $count = $container->aggregate($criteria, $context)
                ->get('count');

            if ($count->getCount()) {
                unset($data[$entityId]);

                continue;
            }

            $item['id'] = $entityId;
        }

        if (!$data) {
            return;
        }

        $container->create(\array_values($data), $context);
    }

    protected function getRepositoryData(): array
    {
        return [];
    }
}
