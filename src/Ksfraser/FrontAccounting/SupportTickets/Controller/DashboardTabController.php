<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\SupportTickets\Controller;

use ksfraser\FrontAccounting\Common\App\AbstractTabController;
use ksfraser\SupportTickets\Service\TicketService;
use ksfraser\SupportTickets\Service\SlaService;

/**
 * DashboardTabController — support team dashboard showing unassigned,
 * SLA-at-risk, and my tickets.
 *
 * RBAC: Support team (SA_ksf_FA_SupportTicketsVIEW) can see all.
 * Customer service reps (SA_ksf_FA_SupportTicketsREAD_ASSIGNED) see only
 * tickets for their assigned customers.
 *
 * @package ksf_FA_SupportTickets
 * @since   1.0.0
 */
class DashboardTabController extends AbstractTabController
{
    private $service;
    private $slaService;

    public function __construct($context = null, array $options = [])
    {
        parent::__construct($context, $options);
        $this->service = new TicketService();
        $this->slaService = new SlaService();
    }

    protected function getPkField(): string
    {
        return 'id';
    }

    protected function getFieldMetadata(): array
    {
        return [
            'entity'      => 'ticket',
            'table'       => '0_ksf_hd_tickets',
            'label'       => 'Support Dashboard',
            'labelPlural' => 'Support Dashboards',
            'hookPrefix'  => 'ticket',
            'pk'          => 'id',
            'fields'      => [],
            'fk_ddls'     => [],
            'ddlHooks'    => [],
            'tableSettings' => [],
        ];
    }

    protected function listRows(int $page, int $perPage): array
    {
        $criteria = [];
        if ($this->userCanReadAssignedOnly() && !empty($this->assignedCustomerIds())) {
            $criteria['requester_id'] = $this->assignedCustomerIds();
        }
        $all = $this->service->searchTickets($criteria);
        $offset = ($page - 1) * $perPage;
        return array_slice($all, $offset, $perPage);
    }

    protected function countRows(): int
    {
        $criteria = [];
        if ($this->userCanReadAssignedOnly() && !empty($this->assignedCustomerIds())) {
            $criteria['requester_id'] = $this->assignedCustomerIds();
        }
        return count($this->service->searchTickets($criteria));
    }

    protected function findRecord(string $pk): ?array
    {
        $dto = $this->service->findTicketById((int)$pk);
        return $dto ? $dto->toArray() : null;
    }

    protected function createRecord(array $data): int
    {
        return 0;
    }

    protected function updateRecord(string $pk, array $data): void
    {
    }

    protected function deleteRecord(string $pk): void
    {
    }

    protected function fkOptions(): array
    {
        return [];
    }

    /**
     * Get unassigned tickets (status_id = 1 Open, assigned_to = 0).
     */
    public function getUnassignedTickets(): array
    {
        $criteria = [];
        if ($this->userCanReadAssignedOnly() && !empty($this->assignedCustomerIds())) {
            $criteria['requester_id'] = $this->assignedCustomerIds();
        }
        $all = $this->service->searchTickets($criteria);
        return array_values(array_filter($all, function ($t) {
            return ($t['status_id'] ?? 0) === 1 && ($t['assigned_to'] ?? 0) === 0;
        }));
    }

    /**
     * Get SLA-at-risk tickets (open, past resolve hours, not yet resolved/closed).
     */
    public function getSlaAtRiskTickets(): array
    {
        $criteria = [];
        if ($this->userCanReadAssignedOnly() && !empty($this->assignedCustomerIds())) {
            $criteria['requester_id'] = $this->assignedCustomerIds();
        }
        $all = $this->service->searchTickets($criteria);
        $atRisk = [];
        foreach ($all as $ticketDto) {
            $dto = \ksfraser\SupportTickets\Dto\TicketDto::fromArray($ticketDto);
            if ($dto->status_id === 1 && $this->slaService->checkSlaBreach($dto)) {
                $atRisk[] = $ticketDto;
            }
        }
        return $atRisk;
    }

    /**
     * Get tickets assigned to the current user.
     */
    public function getMyTickets(): array
    {
        $criteria = [];
        if ($this->userCanReadAssignedOnly() && !empty($this->assignedCustomerIds())) {
            $criteria['requester_id'] = $this->assignedCustomerIds();
        }
        $all = $this->service->searchTickets($criteria);
        $myId = $this->currentUserId();
        return array_values(array_filter($all, function ($t) use ($myId) {
            return ($t['assigned_to'] ?? 0) === $myId && ($t['status_id'] ?? 0) !== 4;
        }));
    }

    /**
     * RBAC: Is the current user restricted to reading tickets for assigned customers only?
     */
    private function userCanReadAssignedOnly(): bool
    {
        return function_exists('user_check_access') && user_check_access(SA_ksf_FA_SupportTicketsREAD_ASSIGNED);
    }

    /**
     * Return the list of customer IDs the current user is responsible for.
     */
    private function assignedCustomerIds(): array
    {
        if (!function_exists('hook_invoke')) {
            return [];
        }
        $data = ['user_id' => $this->currentUserId()];
        hook_invoke('ksf_FA_CRM', 'getAssignedCustomersForUser', $data);
        return is_array($data) && isset($data['customer_ids']) ? (array)$data['customer_ids'] : [];
    }

    /**
     * Current user ID.
     */
    private function currentUserId(): int
    {
        return function_exists('get_current_user_id') ? (int)get_current_user_id() : 0;
    }
}
