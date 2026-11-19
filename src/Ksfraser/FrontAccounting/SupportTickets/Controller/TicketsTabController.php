<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\SupportTickets\Controller;

use ksfraser\FrontAccounting\Common\App\AbstractTabController;
use ksfraser\SupportTickets\Service\TicketService;
use ksfraser\SupportTickets\Service\TicketCommentService;

/**
 * TicketsTabController — controller SRP for the Tickets tab.
 *
 * @package ksf_FA_SupportTickets
 * @since   1.0.0
 */
class TicketsTabController extends AbstractTabController
{
    private $service;
    private $commentService;

    public function __construct($context = null, array $options = [])
    {
        parent::__construct($context, $options);
        $this->service = new TicketService();
        $this->commentService = new TicketCommentService();
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
            'label'       => 'Ticket',
            'labelPlural' => 'Tickets',
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
        // RBAC filter: CSR (sales) can only see tickets for their assigned customers.
        // If they have the READ_ASSIGNED permission but no assigned customers,
        // they see nothing.
        if ($this->userCanReadAssignedOnly()) {
            $assigned = $this->assignedCustomerIds();
            if (empty($assigned)) {
                return [];
            }
            $criteria['requester_id'] = $assigned;
        }
        $offset = ($page - 1) * $perPage;
        return array_slice($this->service->searchTickets($criteria), $offset, $perPage);
    }

    protected function countRows(): int
    {
        $criteria = [];
        if ($this->userCanReadAssignedOnly()) {
            $assigned = $this->assignedCustomerIds();
            if (empty($assigned)) {
                return 0;
            }
            $criteria['requester_id'] = $assigned;
        }
        return count($this->service->searchTickets($criteria));
    }

    protected function findRecord(string $pk): ?array
    {
        $dto = $this->service->findTicketById((int)$pk);
        if ($dto === null) {
            return null;
        }
        // RBAC: CSR can only read tickets for their assigned customers
        if ($this->userCanReadAssignedOnly() && !in_array($dto->requester_id, $this->assignedCustomerIds())) {
            return null;
        }
        return $dto->toArray();
    }

    protected function createRecord(array $data): int
    {
        $dto = $this->service->createTicket($data);
        return $dto->id;
    }

    protected function updateRecord(string $pk, array $data): void
    {
        $this->service->updateTicket((int)$pk, $data);
    }

    protected function deleteRecord(string $pk): void
    {
        $this->service->deleteTicket((int)$pk);
    }

    protected function fkOptions(): array
    {
        return [];
    }

    /**
     * Handle custom action verbs routed by the front controller.
     *
     * Supported actions:
     *   - assign   : assignTicket(int $ticketId, int $assignedTo)
     *   - resolve  : resolveTicket(int $ticketId, int $resolvedBy)
     *   - close    : closeTicket(int $ticketId, int $closedBy)
     *
     * @param string $action
     * @param array  $params
     * @return array ['success' => bool, 'data' => mixed, 'error' => string|null]
     */
    public function runAction(string $action, array $params = []): array
    {
        switch ($action) {
            case 'assign':
                return $this->actionAssign($params);
            case 'resolve':
                return $this->actionResolve($params);
            case 'close':
                return $this->actionClose($params);
            default:
                return ['success' => false, 'data' => null, 'error' => 'Unknown action: ' . $action];
        }
    }

    private function actionAssign(array $params): array
    {
        $ticketId = (int)($params['ticket_id'] ?? 0);
        $assignedTo = (int)($params['assigned_to'] ?? 0);
        if ($ticketId <= 0 || $assignedTo <= 0) {
            return ['success' => false, 'data' => null, 'error' => 'Invalid ticket_id or assigned_to'];
        }
        if (!$this->userCanAssign()) {
            return ['success' => false, 'data' => null, 'error' => 'Not authorized to assign tickets'];
        }
        try {
            $ticket = $this->service->assignTicket($ticketId, $assignedTo);
            hook_invoke_all('ticket_assigned', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'assigned_to' => $assignedTo,
                'assigned_by' => $this->currentUserId(),
            ]);
            return ['success' => true, 'data' => $ticket->toArray(), 'error' => null];
        } catch (\Exception $e) {
            return ['success' => false, 'data' => null, 'error' => $e->getMessage()];
        }
    }

    private function actionResolve(array $params): array
    {
        $ticketId = (int)($params['ticket_id'] ?? 0);
        $resolvedBy = (int)($params['resolved_by'] ?? 0);
        if ($ticketId <= 0 || $resolvedBy <= 0) {
            return ['success' => false, 'data' => null, 'error' => 'Invalid ticket_id or resolved_by'];
        }
        if (!$this->userCanResolve()) {
            return ['success' => false, 'data' => null, 'error' => 'Not authorized to resolve tickets'];
        }
        try {
            $ticket = $this->service->resolveTicket($ticketId, $resolvedBy);
            hook_invoke_all('ticket_resolved', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'resolved_by' => $resolvedBy,
            ]);
            return ['success' => true, 'data' => $ticket->toArray(), 'error' => null];
        } catch (\Exception $e) {
            return ['success' => false, 'data' => null, 'error' => $e->getMessage()];
        }
    }

    private function actionClose(array $params): array
    {
        $ticketId = (int)($params['ticket_id'] ?? 0);
        $closedBy = (int)($params['closed_by'] ?? 0);
        if ($ticketId <= 0 || $closedBy <= 0) {
            return ['success' => false, 'data' => null, 'error' => 'Invalid ticket_id or closed_by'];
        }
        if (!$this->userCanClose()) {
            return ['success' => false, 'data' => null, 'error' => 'Not authorized to close tickets'];
        }
        try {
            $ticket = $this->service->closeTicket($ticketId, $closedBy);
            hook_invoke_all('ticket_closed', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'closed_by' => $closedBy,
            ]);
            return ['success' => true, 'data' => $ticket->toArray(), 'error' => null];
        } catch (\Exception $e) {
            return ['success' => false, 'data' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * RBAC: Can the current user assign tickets?
     */
    private function userCanAssign(): bool
    {
        return function_exists('user_check_access') && user_check_access(SA_ksf_FA_SupportTicketsASSIGN);
    }

    /**
     * RBAC: Can the current user resolve tickets?
     */
    private function userCanResolve(): bool
    {
        return function_exists('user_check_access') && user_check_access(SA_ksf_FA_SupportTicketsRESOLVE);
    }

    /**
     * RBAC: Can the current user close tickets?
     */
    private function userCanClose(): bool
    {
        return function_exists('user_check_access') && user_check_access(SA_ksf_FA_SupportTicketsCLOSE);
    }

    /**
     * RBAC: Is the current user restricted to reading tickets for assigned customers only?
     * (Customer service reps / sales can see tickets for their assigned customers but not change status.)
     */
    private function userCanReadAssignedOnly(): bool
    {
        return function_exists('user_check_access') && user_check_access(SA_ksf_FA_SupportTicketsREAD_ASSIGNED);
    }

    /**
     * Return the list of customer IDs the current user is responsible for.
     * Falls back to empty array (no restriction) if the CRM resolver is unavailable.
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
