<?php

declare(strict_types=1);

namespace Kanakhin\Push\Infrastructure\Repository;

use Kanakhin\Push\Domain\Entity\Site;
use Kanakhin\Push\Domain\Entity\Subscription;
use Kanakhin\Push\Domain\Repository\SubscriberRepositoryInterface;
use Kanakhin\Push\Infrastructure\Config;

class PdoSubscriberRepository implements SubscriberRepositoryInterface
{
    private \PDO $db;
    private Config $config;

    public function __construct(\PDO $db, Config $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    /**
     * @param array $filters
     * @return array|null
     */
    public function findSubscribers(array $filters): ?array
    {
        $site_condition = '';
        $tstamp_condition_from = '';
        $tstamp_condition_to = '';

        if (!empty($filters['sites'])) {
            $site_condition = "AND `sub`.`site_id` IN (" . implode(",", $filters['sites']) . ")";
        }
        if (!empty($filters['subscribe_time_start'])) {
            $tstamp_condition_from = "AND `sub`.`tstamp` >= '" . $filters['subscribe_time_start'] . "'";
        }
        if (!empty($filters['subscribe_time_end'])) {
            $tstamp_condition_to = "AND `sub`.`tstamp` <= '" . $filters['subscribe_time_end'] . "'";
        }

        $query = $this->db->query("SELECT `sub`.*, s.`host`, s.`vapid_key_public`, s.`vapid_key_private` FROM `subscriptions` AS `sub`
        LEFT JOIN `sites` AS s ON (sub.`site_id` = s.`id`)
        WHERE TRUE $site_condition $tstamp_condition_from $tstamp_condition_to;");
        $subscriptions = $query->fetchAll();

        $result = [];
        foreach ($subscriptions AS $subscription) {
            $result[] = $subscription;
        }

        return $result;
    }

    public function subscribe(Site $site, string $endpoint, string $key, string $token, string $ip = '', string $useragent = ''): bool
    {
        $query = $this->db->prepare("
			INSERT INTO `subscriptions` (`site_id`, `endpoint_id`, `endpoint`, `key`, `token`, `ip`, `useragent`)
			VALUES (:site_id, :endpoint_id, :endpoint, :key, :token, :ip, :useragent)
		");

        try {
            $query->execute([
                ':site_id' => $site->getId(),
                ':endpoint_id' => md5($endpoint),
                ':endpoint' => $endpoint,
                ':key' => $key,
                ':token' => $token,
                ':ip' => $ip,
                ':useragent' => $useragent,
            ]);

            $state = true;
        } catch (\PDOException $e) {
            $state = false;
        }

        return $state;
    }


}