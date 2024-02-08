<?php

declare(strict_types=1);

namespace Kanakhin\Push\Infrastructure\Repository;

use GuzzleHttp\Psr7\Request;
use Kanakhin\Push\Domain\Entity\Site;
use Kanakhin\Push\Domain\Repository\SiteRepositoryInterface;
use Kanakhin\Push\Infrastructure\Config;

class PdoSiteRepository implements SiteRepositoryInterface
{
    private \PDO $db;
    private Config $config;
    public function __construct(\PDO $db, Config $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    public function getServiceHost(bool $with_protocol = false): string
    {
        if ($with_protocol) {
            return "https://" . $this->config->get("service_host");
        } else {
            return $this->config->get("service_host");
        }
    }

    public function findOneById(int $id): ?Site
    {
        $query = $this->db->prepare("SELECT * FROM `sites` WHERE `id` = :id;");
        $query->execute([':id' => $id]);
        $site_data = $query->fetch();

        if (empty($site_data)) {
            return null;
        }

        $site = new Site(
            $site_data['hash_id'],
            $site_data['host'],
            $site_data['vapid_key_public'],
            $site_data['vapid_key_private']
        );

        $reflection_class = new \ReflectionClass($site);
        $reflection_class->getProperty('id')->setValue($site, $site_data['id']);

        return $site;
    }

    /**
     * @return array|Site[]
     */
    public function findAll(): array
    {
        $query = $this->db->query("SELECT * FROM `sites`;");
        $sites = $query->fetchAll();

        if (empty($sites)) {
            return [];
        }

        $result = [];
        foreach ($sites AS $site_data) {
            $site = new Site(
                $site_data['hash_id'],
                $site_data['host'],
                $site_data['vapid_key_public'],
                $site_data['vapid_key_private']
            );

            $reflection_class = new \ReflectionClass($site);
            $reflection_class->getProperty('id')->setValue($site, $site_data['id']);

            $result[] = $site;
        }

        return $result;
    }

    public function findByHash(string $hash): ?Site
    {
        $query = $this->db->prepare("SELECT * FROM `sites` WHERE `hash_id` = :hash;");
        $query->execute([':hash' => $hash]);
        $site_data = $query->fetch();

        if (empty($site_data)) {
            return null;
        }

        $site = new Site(
            $site_data['hash_id'],
            $site_data['host'],
            $site_data['vapid_key_public'],
            $site_data['vapid_key_private']
        );

        $reflection_class = new \ReflectionClass($site);
        $reflection_class->getProperty('id')->setValue($site, $site_data['id']);

        return $site;
    }

    public function findByHost(string $host): ?Site
    {
        $query = $this->db->prepare("SELECT * FROM `sites` WHERE `host` = :host;");
        $query->execute([':host' => $host]);
        $site_data = $query->fetch();

        if (empty($site_data)) {
            return null;
        }

        $site = new Site(
            $site_data['hash_id'],
            $site_data['host'],
            $site_data['vapid_key_public'],
            $site_data['vapid_key_private']
        );

        $reflection_class = new \ReflectionClass($site);
        $reflection_class->getProperty('id')->setValue($site, $site_data['id']);

        return $site;
    }

    public function findByKeyPublic(string $public_key): ?Site
    {
        $query = $this->db->prepare("SELECT * FROM `sites` WHERE `vapid_key_public` = :public_key;");
        $query->execute([':public_key' => $public_key]);
        $site_data = $query->fetch();

        if (empty($site_data)) {
            return null;
        }

        $site = new Site(
            $site_data['hash_id'],
            $site_data['host'],
            $site_data['vapid_key_public'],
            $site_data['vapid_key_private']
        );

        $reflection_class = new \ReflectionClass($site);
        $reflection_class->getProperty('id')->setValue($site, $site_data['id']);

        return $site;
    }

    public function save(Site $entity): ?int
    {
        $query = $this->db->prepare("INSERT INTO `sites` (`hash_id`, `host`, `vapid_key_public`, `vapid_key_private`) VALUES (:hash_id, :host, :public, :private);");
        try {
            $query->execute([
                ':hash_id' => $entity->getHashId(),
                ':host' => $entity->getHost(),
                ':public' => $entity->getVapidKeyPublic(),
                ':private' => $entity->getVapidKeyPrivate()
            ]);
            return (int)$this->db->lastInsertId();
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function delete(Site $entity): void
    {
        $query = $this->db->prepare("DELETE FROM `sites` WHERE `id` = :id;");
        $query->execute([':id' => $entity->getId()]);
    }

    public function getUrl(Site $site): string
    {
        return 'https://' . idn_to_ascii($site->getHost());
    }

    public function checkIntegration(Site $site): bool
    {
        try {
            $this->javascriptCheckers[] = [
                'site' => $site,
                'promise' => $this->httpclient->sendAsync(new Request('GET', $site->getUrl())),
            ];

            $this->workerFileCheckers[] = [
                'site' => $site,
                'promise' => $this->httpclient->sendAsync(new Request('GET', $site->getUrl() . '/' . $this->workerfile->getWorkertFileName())),
            ];

            //Чекер делает проверочные запросы асинхронно, поэтому ограничиваем количество паралледьных проверок
            if (++$this->counter > 300) {
                $this->flush();
            }

            return $this;
        } catch (Exception $e) {

        }
    }


}