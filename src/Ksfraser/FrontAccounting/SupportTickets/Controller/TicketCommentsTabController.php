<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\SupportTickets\Controller;

use ksfraser\FrontAccounting\Common\App\AbstractTabController;
use ksfraser\SupportTickets\Service\TicketCommentService;

/**
 * TicketCommentsTabController — controller SRP for Ticket Comments.
 *
 * @package ksf_FA_SupportTickets
 * @since   1.0.0
 */
class TicketCommentsTabController extends AbstractTabController
{
    private $service;

    public function __construct($context = null, array $options = [])
    {
        parent::__construct($context, $options);
        $this->service = new TicketCommentService();
    }

    protected function getPkField(): string
    {
        return 'id';
    }

    protected function getFieldMetadata(): array
    {
        return [
            'entity'      => 'ticket_comment',
            'table'       => '0_ksf_hd_ticket_comments',
            'label'       => 'Comment',
            'labelPlural' => 'Comments',
            'hookPrefix'  => 'ticket_comment',
            'pk'          => 'id',
            'fields'      => [],
            'fk_ddls'     => [],
            'ddlHooks'    => [],
            'tableSettings' => [],
        ];
    }

    protected function listRows(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $ticketId = isset($_GET['ticket_id']) ? (int)$_GET['ticket_id'] : 0;
        $comments = $this->service->getCommentsForTicket($ticketId);
        return array_slice($comments, $offset, $perPage);
    }

    protected function countRows(): int
    {
        $ticketId = isset($_GET['ticket_id']) ? (int)$_GET['ticket_id'] : 0;
        return count($this->service->getCommentsForTicket($ticketId));
    }

    protected function findRecord(string $pk): ?array
    {
        $comments = $this->service->getCommentsForTicket(0);
        foreach ($comments as $c) {
            if ($c->id == $pk) {
                return $c->toArray();
            }
        }
        return null;
    }

    protected function createRecord(array $data): int
    {
        $dto = $this->service->addComment($data);
        return $dto->id;
    }

    protected function updateRecord(string $pk, array $data): void
    {
        // Comments are typically not updated
    }

    protected function deleteRecord(string $pk): void
    {
        $this->service->deleteComment((int)$pk);
    }

    protected function fkOptions(): array
    {
        return [];
    }
}
