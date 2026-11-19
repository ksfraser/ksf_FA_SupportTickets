<?php
declare(strict_types=1);

namespace Tests\Unit;

use ksfraser\SupportTickets\Service\TicketService;
use ksfraser\SupportTickets\Service\TicketCommentService;
use ksfraser\SupportTickets\Service\SlaService;
use ksfraser\SupportTickets\Service\TicketCategoryService;
use PHPUnit\Framework\TestCase;

/**
 * @BABOK Related: UT-HD-001-001-002
 * @since 1.0.0
 */
class SupportTicketsAdapterIntegrationTest extends TestCase
{
    public function testTicketServiceIsInstantiable(): void
    {
        $service = new TicketService();
        $this->assertInstanceOf(TicketService::class, $service);
    }

    public function testTicketCommentServiceIsInstantiable(): void
    {
        $service = new TicketCommentService();
        $this->assertInstanceOf(TicketCommentService::class, $service);
    }

    public function testSlaServiceIsInstantiable(): void
    {
        $service = new SlaService();
        $this->assertInstanceOf(SlaService::class, $service);
    }

    public function testTicketCategoryServiceIsInstantiable(): void
    {
        $service = new TicketCategoryService();
        $this->assertInstanceOf(TicketCategoryService::class, $service);
    }

    public function testFullSupportWorkflow(): void
    {
        $ticketService = new TicketService();
        $commentService = new TicketCommentService();
        $slaService = new SlaService();

        // Create SLA
        $sla = $slaService->createSla([
            'name' => 'Standard',
            'response_hours' => 4,
            'resolve_hours' => 24,
        ]);

        // Create ticket
        $ticket = $ticketService->createTicket([
            'subject' => 'Printer broken',
            'description' => 'Office printer will not power on',
            'requester_id' => 10,
            'category_id' => 1,
            'priority_id' => 2,
            'sla_id' => $sla->id,
        ]);

        $this->assertSame(1, $ticket->id);
        $this->assertSame('TK-001000', $ticket->ticket_number);

        // Assign ticket
        $assigned = $ticketService->assignTicket($ticket->id, 25);
        $this->assertSame(25, $assigned->assigned_to);

        // Add comment
        $comment = $commentService->addComment([
            'ticket_id' => $ticket->id,
            'author_id' => 25,
            'comment' => 'Technician dispatched',
            'is_internal' => true,
        ]);
        $this->assertSame(1, $comment->id);
        $this->assertSame($ticket->id, $comment->ticket_id);

        // Resolve ticket
        $resolved = $ticketService->resolveTicket($ticket->id, 25);
        $this->assertSame(3, $resolved->status_id);
        $this->assertNotNull($resolved->resolved_at);

        // Check SLA breach
        $ticketDto = new \ksfraser\SupportTickets\Dto\TicketDto([
            'id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'sla_id' => $sla->id,
            'status_id' => 1,
            'created_at' => date('Y-m-d H:i:s', strtotime('-30 hours')),
        ]);
        $this->assertTrue($slaService->checkSlaBreach($ticketDto));
    }
}
