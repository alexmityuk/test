<?php

use App\Checkout\Discount\Service\CheckoutDiscountService;
use App\Checkout\Entity\Checkout;
use App\Order\Entity\Order;
use App\Order\Service\OrderService;

class CheckoutService
{
    public function __construct(
        private readonly CheckoutDiscountService $checkoutDiscountService,
        private readonly OrderService $orderService,
    ) {
    }

    public function createOrder(Checkout $checkout): Order
    {
        $totalPrice = array_sum(
            array_map(
                fn($cartLine) => $cartLine->getQuantity() * $cartLine->getPrice(),
                $checkout->getCartLines()
            )
        );
        $checkout->setTotalPrice($totalPrice);
        $discount = $this->checkoutDiscountService->calculateDiscount($checkout);
        $checkout->setTotalPrice($checkout->getTotalPrice() - $discount);

        return $this->orderService->createOrder($checkout);
    }
}
