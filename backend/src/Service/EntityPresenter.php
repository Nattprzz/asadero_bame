<?php

// ─────────────────────────────────────────────────────────────────────────────
// EntityPresenter.php — presentación de entidades para la API.
//
// Este servicio transforma las entidades principales de la aplicación en arrays
// preparados para respuestas JSON. Se usa para mantener un formato de salida
// uniforme sin exponer directamente las entidades de Doctrine.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Service;

use App\Entity\Allergen;
use App\Entity\Category;
use App\Entity\CustomerOrder;
use App\Entity\Local;
use App\Entity\LocalProduct;
use App\Entity\OrderLine;
use App\Entity\Product;
use App\Entity\User;

final class EntityPresenter
{
    // Devuelve los datos públicos de un usuario.
    public function user(User $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'surname' => $user->getSurname(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'roles' => $user->getRoles(),
            'localId' => $user->getLocal()?->getId(),
            'createdAt' => $this->date($user->getCreatedAt()),
            'updatedAt' => $this->date($user->getUpdatedAt()),
        ];
    }

    // Devuelve una categoría con sus datos principales.
    public function category(Category $category): array
    {
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
            'description' => $category->getDescription(),
            'imageUrl' => $category->getImageUrl(),
            'active' => $category->isActive(),
            'sortOrder' => $category->getSortOrder(),
            'createdAt' => $this->date($category->getCreatedAt()),
            'updatedAt' => $this->date($category->getUpdatedAt()),
        ];
    }

    // Devuelve la información de un alérgeno.
    public function allergen(Allergen $allergen): array
    {
        return [
            'id' => $allergen->getId(),
            'name' => $allergen->getName(),
            'slug' => $allergen->getSlug(),
            'description' => $allergen->getDescription(),
            'iconUrl' => $allergen->getIconUrl(),
            'createdAt' => $this->date($allergen->getCreatedAt()),
            'updatedAt' => $this->date($allergen->getUpdatedAt()),
        ];
    }

    // Devuelve un producto junto con su categoría y alérgenos.
    public function product(Product $product): array
    {
        return [
            'id' => $product->getId(),
            'categoryId' => $product->getCategory()?->getId(),
            'category' => $product->getCategory() ? $this->category($product->getCategory()) : null,
            'name' => $product->getName(),
            'slug' => $product->getSlug(),
            'description' => $product->getDescription(),
            'price' => (float) $product->getPrice(),
            'available' => $product->isAvailable(),
            'availability' => $product->getAvailability(),
            'featured' => $product->isFeatured(),
            'weight' => $product->getWeight(),
            'prepTime' => $product->getPrepTime(),
            'imagePath' => $product->getImagePath(),

            // Los alérgenos se presentan con el mismo formato que en el resto de la API.
            'allergens' => array_map(fn (Allergen $a) => $this->allergen($a), $product->getAllergens()->toArray()),

            'createdAt' => $this->date($product->getCreatedAt()),
            'updatedAt' => $this->date($product->getUpdatedAt()),
        ];
    }

    // Devuelve la información de un local.
    public function local(Local $local): array
    {
        return [
            'id' => $local->getId(),
            'name' => $local->getName(),
            'address' => $local->getAddress(),
            'city' => $local->getCity(),
            'postalCode' => $local->getPostalCode(),
            'phone' => $local->getPhone(),
            'email' => $local->getEmail(),
            'latitude' => $local->getLatitude() === null ? null : (float) $local->getLatitude(),
            'longitude' => $local->getLongitude() === null ? null : (float) $local->getLongitude(),
            'hours' => $local->getHours(),
            'reservationHours' => $local->getReservationHours(),
            'active' => $local->isActive(),
            'status' => $local->getStatus(),
            'whatsapp' => $local->getWhatsapp(),
            'createdAt' => $this->date($local->getCreatedAt()),
            'updatedAt' => $this->date($local->getUpdatedAt()),
        ];
    }

    // Devuelve los datos de stock de un producto en un local.
    public function stock(LocalProduct $stock): array
    {
        return [
            'id' => $stock->getId(),
            'localId' => $stock->getLocal()?->getId(),
            'productId' => $stock->getProduct()?->getId(),
            'stock' => $stock->getStock(),
            'available' => $stock->isAvailable(),
            'createdAt' => $this->date($stock->getCreatedAt()),
            'updatedAt' => $this->date($stock->getUpdatedAt()),
        ];
    }

    // Devuelve un pedido con su local y sus líneas asociadas.
    public function order(CustomerOrder $order): array
    {
        $user = $order->getUser();
        $clientName = $user
            ? trim($user->getName() . ' ' . ($user->getSurname() ?? ''))
            : null;

        return [
            'id' => $order->getId(),
            'reference' => $order->getReference(),
            'userId' => $user?->getId(),
            'clientName' => $clientName ?: null,
            'localId' => $order->getLocal()?->getId(),
            'local' => $order->getLocal() ? $this->local($order->getLocal()) : null,
            'status' => $order->getStatus(),
            'type' => $order->getType(),
            'total' => (float) $order->getTotal(),
            'paymentMethod' => $order->getPaymentMethod(),
            'paymentStatus' => $order->getPaymentStatus(),
            'paidAt' => $order->getPaidAt() ? $this->date($order->getPaidAt()) : null,
            'notes' => $order->getNotes(),
            'estimatedTime' => $order->getEstimatedTime(),
            'phone' => $order->getPhone(),
            'address' => $order->getAddress(),

            // Cada línea se presenta por separado para mantener el pedido legible.
            'lines' => array_map(fn (OrderLine $line) => $this->orderLine($line), $order->getLines()->toArray()),

            'createdAt' => $this->date($order->getCreatedAt()),
            'updatedAt' => $this->date($order->getUpdatedAt()),
        ];
    }

    // Devuelve una línea de pedido junto con el producto asociado.
    public function orderLine(OrderLine $line): array
    {
        return [
            'id' => $line->getId(),
            'orderId' => $line->getOrder()?->getId(),
            'productId' => $line->getProduct()?->getId(),
            'product' => $line->getProduct() ? $this->product($line->getProduct()) : null,
            'quantity' => $line->getQuantity(),
            'unitPrice' => (float) $line->getUnitPrice(),
            'notes' => $line->getNotes(),
            'createdAt' => $this->date($line->getCreatedAt()),
            'updatedAt' => $this->date($line->getUpdatedAt()),
        ];
    }

    // Formatea las fechas en un formato estándar para la API.
    private function date(\DateTimeInterface $date): string
    {
        return $date->format(\DateTimeInterface::ATOM);
    }
}
