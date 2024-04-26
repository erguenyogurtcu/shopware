<?php

namespace Scripts\Examples;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Uuid\Uuid;

require_once __DIR__ . '/examples/base-script.php';

$env = 'dev'; // by default, kernel gets booted in dev

$kernel = require __DIR__ . '/boot/boot.php';

class Main extends BaseScript
{
    public function run()
    {
        $definition = $this->getContainer()->get('my_entity.definition');

        /** @var EntityRepository $repo */
        $repo = $this->getContainer()->get('my_entity.repository');

        $data = [
            ['id' => Uuid::randomHex(), 'name' => 'foo', 'number' => 'foo']
        ];

        $repo->upsert($data, Context::createCLIContext());

        $entities = $repo->search(new Criteria(), Context::createCLIContext());

        dd(json_decode(json_encode($entities), true));
    }
}


(new Main($kernel))->run();
