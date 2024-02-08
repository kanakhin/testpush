<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase;

use Kanakhin\Push\Application\UseCase\Request\CreateMessageRequest;
use Kanakhin\Push\Application\UseCase\Response\CreateMessageResponse;
use Kanakhin\Push\Domain\Entity\Message;
use Kanakhin\Push\Domain\Repository\MessageRepositoryInterface;
use Kanakhin\Push\Domain\Repository\SiteRepositoryInterface;

class CreateMessageUseCase
{
    private SiteRepositoryInterface $siteRepository;
    private MessageRepositoryInterface $messageRepository;

    /**
     * @param MessageRepositoryInterface $messageRepository
     */
    public function __construct(SiteRepositoryInterface $siteRepository, MessageRepositoryInterface $messageRepository)
    {
        $this->siteRepository = $siteRepository;
        $this->messageRepository = $messageRepository;
    }

    public function __invoke(CreateMessageRequest $request): CreateMessageResponse
    {
        $sites = [];
        if (!empty($request->sites)) {
            foreach ($request->sites AS $host) {
                $site = $this->siteRepository->findByHost($host);

                if (!is_null($site)) {
                    $sites[] = $site->getId();
                }
            }
        } else {
            $all_sites = $this->siteRepository->findAll();
            foreach ($all_sites AS $site) {
                $sites[] = $site->getId();
            }
        }

        //Подготовка фильтров
        $filters = json_encode([
            'subscribe_time_start' => $request->subscribe_from->format("Y-m-d H:i:s"),
            'subscribe_time_end' => $request->subscribe_to->format("Y-m-d H:i:s"),
            'sites' => $sites,
        ]);

        $message = new Message(
            new \DateTimeImmutable(),
            $request->send_time,
            $filters,
            $request->title,
            $request->text,
            $request->link,
            0
        );

        $id = $this->messageRepository->save($message);

        if (is_null($id)) {
            return new CreateMessageResponse(null);
        }

        return new CreateMessageResponse($id);
    }

}