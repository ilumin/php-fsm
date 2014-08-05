<?php
use Carbon\Carbon;
use Finite\Exception\StateException;

class OrderFsmTest extends TestCase
{
    private $order;
    private $machine;
    public function setUp() {
        parent::setUp();
        $this->order = new OrderFsm();
        $this->machine = $this->order->getStateMachine();
    }
    public function testInitialStatePlaceOrder() {
        $currentState = $this->machine->getCurrentState();
        $this->assertSame('PlacedOrder', $currentState->getName());
        // Check can refund
        $orderGuard = new OrderFsmGuard($this->order);
        $this->assertFalse($orderGuard->canRefund());
        $this->assertTrue($currentState->get('payable'));
    }

    public function testBuyerPayFail() {
        $this->machine->apply('BuyerPayFail');
        $currentState = $this->machine->getCurrentState();
        $this->assertSame('Incompleted', $currentState->getName());
    }

    public function testBuyerPaySuccess() {
        $this->machine->apply('BuyerPaySuccess');
        $currentState = $this->machine->getCurrentState();
        $this->assertSame('Paid', $currentState->getName());
        $this->assertTrue($currentState->get('refundable'));
    }

    public function testBuyerClickRefundFailed() {
        App::singleton('OrderFsmGuard', function () {
            $mock = $this->getMockBuilder('OrderFsmGuard')->enableOriginalConstructor()->setConstructorArgs(array(
                $this->order
            ))->setMethods(array(
                'canRefund'
            ))->getMock();
            $mock->expects($this->any())->method('canRefund')->will($this->returnValue(false));
            return $mock;
        });

        $this->order = new OrderFsm();
        $this->machine = $this->order->getStateMachine();
        $this->machine->apply('BuyerPaySuccess');
        try {
            $this->machine->apply('BuyerClickRefund');
        }
        catch(StateException $e) {
            $this->assertSame('The "BuyerClickRefund" transition can not be applied to the "Paid" state of object "OrderFsm" with graph "".', $e->getMessage());
            $currentState = $this->machine->getCurrentState();
            $this->assertSame('Paid', $currentState->getName());
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testBuyerClickRefundSuccess() {
        App::singleton('OrderFsmGuard', function () {
            $mock = $this->getMockBuilder('OrderFsmGuard')->enableOriginalConstructor()->setConstructorArgs(array(
                $this->order
            ))->setMethods(array(
                'canRefund'
            ))->getMock();
            $mock->expects($this->once())->method('canRefund')->will($this->returnValue(true));
            return $mock;
        });
        $this->order = new OrderFsm();

        $this->machine = $this->order->getStateMachine();
        $this->machine->apply('BuyerPaySuccess');
        $this->machine->apply('BuyerClickRefund');
    }
}
