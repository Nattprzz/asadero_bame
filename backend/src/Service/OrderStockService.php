<?php

namespace App\Service;

use App\Entity\CustomerOrder;
use App\Entity\Local;
use App\Entity\Product;
use App\Enum\ProductAvailability;
use App\Repository\LocalProductRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class OrderStockService
{
    public function __construct(
        private readonly LocalProductRepository $localProducts,
    ) {}

    public function reserve(Local $local, CustomerOrder $order): void
    {
        $quantitiesByProduct = $this->quantitiesByProduct($order);

        if ($quantitiesByProduct === []) {
            return;
        }

        $productIds = array_keys($quantitiesByProduct);
        sort($productIds, SORT_NUMERIC);
        $stocks = $this->localProducts->findForUpdate($local, $productIds);
        $stockByProduct = $this->stockByProduct($stocks);

        foreach ($productIds as $productId) {
            $stock = $stockByProduct[$productId] ?? null;

            if ($stock === null) {
                throw new UnprocessableEntityHttpException('El producto no pertenece al local seleccionado.');
            }

            $product = $stock->getProduct();

            if (
                !$product instanceof Product
                || !$product->isAvailable()
                || in_array($product->getAvailability(), [ProductAvailability::HIDDEN, ProductAvailability::SOLD_OUT], true)
            ) {
                throw new UnprocessableEntityHttpException('Producto no disponible.');
            }

            if ($stock->getStock() < $quantitiesByProduct[$productId]) {
                throw new UnprocessableEntityHttpException(sprintf('Stock insuficiente para %s.', $product->getName()));
            }
        }

        foreach ($quantitiesByProduct as $productId => $quantity) {
            $stockByProduct[$productId]->setStock($stockByProduct[$productId]->getStock() - $quantity);
        }
    }

    public function restore(CustomerOrder $order): void
    {
        $local = $order->getLocal();
        $quantitiesByProduct = $this->quantitiesByProduct($order);

        if ($quantitiesByProduct === []) {
            return;
        }

        if ($local === null) {
            throw new UnprocessableEntityHttpException('El pedido no tiene local para reponer el stock.');
        }

        $productIds = array_keys($quantitiesByProduct);
        sort($productIds, SORT_NUMERIC);
        $stocks = $this->localProducts->findForUpdate($local, $productIds);
        $stockByProduct = $this->stockByProduct($stocks);

        foreach ($productIds as $productId) {
            $stock = $stockByProduct[$productId] ?? null;

            if ($stock === null) {
                throw new UnprocessableEntityHttpException('No existe stock local para reponer un producto del pedido.');
            }

            $stock->setStock($stock->getStock() + $quantitiesByProduct[$productId]);
        }
    }

    /**
     * @return array<int, int>
     */
    private function quantitiesByProduct(CustomerOrder $order): array
    {
        $quantitiesByProduct = [];

        foreach ($order->getLines() as $line) {
            $productId = $line->getProduct()?->getId();

            if ($productId === null || $line->getQuantity() < 1) {
                throw new BadRequestHttpException('Linea de pedido sin producto.');
            }

            $quantitiesByProduct[$productId] = ($quantitiesByProduct[$productId] ?? 0) + $line->getQuantity();
        }

        return $quantitiesByProduct;
    }

    /**
     * @param array<int, \App\Entity\LocalProduct> $stocks
     * @return array<int, \App\Entity\LocalProduct>
     */
    private function stockByProduct(array $stocks): array
    {
        $stockByProduct = [];

        foreach ($stocks as $stock) {
            $productId = $stock->getProduct()?->getId();

            if ($productId !== null) {
                $stockByProduct[$productId] = $stock;
            }
        }

        return $stockByProduct;
    }
}
