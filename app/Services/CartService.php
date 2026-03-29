<?php

namespace App\Services;

use App\Models\Product;

/**
 * CartService
 * 
 * Handles all shopping cart operations.
 * Cart is stored in session with the following structure:
 * [
 *     'item_key' => [
 *         'product_id' => int,
 *         'name' => string,
 *         'price' => float,
 *         'quantity' => int,
 *         'image' => string (nullable),
 *         'custom_dimensions' => array (nullable),
 *     ]
 * ]
 */
class CartService
{
    const SESSION_KEY = 'cart';

    /**
     * Get all cart items from session
     *
     * @return array
     */
    public function getCart(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    /**
     * Get cart with enriched product data
     * 
     * Fetches product details from database to ensure current data
     * (images, pricing from product table, etc.)
     *
     * @return array
     */
    public function getEnrichedCart(): array
    {
        $cart = $this->getCart();
        $enrichedCart = [];

        foreach ($cart as $itemKey => $item) {
            $enrichedCart[$itemKey] = $item;

            // Enrich with current product data
            if (!empty($item['product_id'])) {
                $product = Product::find($item['product_id']);
                if ($product && $product->images) {
                    $images = is_array($product->images) 
                        ? $product->images 
                        : json_decode($product->images, true);
                    
                    if (!empty($images) && isset($images[0])) {
                        $enrichedCart[$itemKey]['image'] = $images[0];
                    }
                }
            }
        }

        return $enrichedCart;
    }

    /**
     * Add product to cart
     *
     * @param int $productId
     * @param string $productName
     * @param float $price
     * @param int $quantity
     * @param string|null $image
     * @param array|null $customDimensions
     * @return string Item key of added item
     */
    public function addItem(
        int $productId,
        string $productName,
        float $price,
        int $quantity,
        ?string $image = null,
        ?array $customDimensions = null
    ): string {
        $cart = $this->getCart();

        // Generate item key based on product_id and custom dimensions
        $itemKey = $this->generateItemKey($productId, $customDimensions);

        if (isset($cart[$itemKey])) {
            // If item already in cart, increase quantity
            $cart[$itemKey]['quantity'] += $quantity;
            
            // Update image if provided
            if ($image !== null) {
                $cart[$itemKey]['image'] = $image;
            }
        } else {
            // Add new item to cart
            $cart[$itemKey] = [
                'product_id' => $productId,
                'name' => $productName,
                'price' => (float) $price,
                'quantity' => $quantity,
                'image' => $image,
                'custom_dimensions' => $customDimensions,
            ];
        }

        $this->saveCart($cart);

        return $itemKey;
    }

    /**
     * Update item quantity in cart
     *
     * @param string $itemKey
     * @param int $quantity
     * @return bool
     * @throws \Exception
     */
    public function updateItemQuantity(string $itemKey, int $quantity): bool
    {
        if ($quantity < 1) {
            throw new \Exception('Jumlah barang harus minimal 1.');
        }

        $cart = $this->getCart();

        if (!isset($cart[$itemKey])) {
            throw new \Exception('Produk tidak ditemukan di dalam keranjang.');
        }

        $cart[$itemKey]['quantity'] = $quantity;
        $this->saveCart($cart);

        return true;
    }

    /**
     * Remove single item from cart
     *
     * @param string $itemKey
     * @return bool
     * @throws \Exception
     */
    public function removeItem(string $itemKey): bool
    {
        $cart = $this->getCart();

        if (!isset($cart[$itemKey])) {
            throw new \Exception('Produk tidak ditemukan di keranjang.');
        }

        unset($cart[$itemKey]);
        $this->saveCart($cart);

        return true;
    }

    /**
     * Clear entire cart
     *
     * @return void
     */
    public function clearCart(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * Check if cart is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->getCart());
    }

    /**
     * Get total cart amount
     *
     * @return float
     */
    public function getTotal(): float
    {
        $cart = $this->getCart();
        
        return collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    /**
     * Get total items count
     *
     * @return int
     */
    public function getItemCount(): int
    {
        $cart = $this->getCart();
        
        return collect($cart)->sum(fn($item) => $item['quantity']);
    }

    /**
     * Get specific item from cart
     *
     * @param string $itemKey
     * @return array|null
     */
    public function getItem(string $itemKey): ?array
    {
        $cart = $this->getCart();
        
        return $cart[$itemKey] ?? null;
    }

    /**
     * Generate unique item key based on product_id and custom dimensions
     *
     * @param int $productId
     * @param array|null $customDimensions
     * @return string
     */
    private function generateItemKey(int $productId, ?array $customDimensions = null): string
    {
        $key = (string) $productId;

        if (!empty($customDimensions)) {
            $key .= '_' . md5(json_encode($customDimensions));
        }

        return $key;
    }

    /**
     * Save cart to session
     *
     * @param array $cart
     * @return void
     */
    private function saveCart(array $cart): void
    {
        session()->put(self::SESSION_KEY, $cart);
    }
}
