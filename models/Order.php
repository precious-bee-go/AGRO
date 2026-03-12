<?php
namespace Models;

use Core\Model;

class Order extends Model
{
    /**
     * Create a new order and its items
     */
    public function create($buyerId, $cartData, $address)
    {
        $this->db->begin_transaction();

        try {
            $total = $cartData['total'];
            $advance = $total * BOOKING_PERCENTAGE;
            $remaining = $total - $advance;

            // 1. Insert Order
            $sql = "INSERT INTO orders (buyer_id, total_amount, advance_amount, remaining_amount, delivery_address, order_status) 
                    VALUES (?, ?, ?, ?, ?, 'booked')";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("iddds", $buyerId, $total, $advance, $remaining, $address);
            $stmt->execute();
            $orderId = $this->db->insert_id;

            // 2. Insert Items and update batch quantities
            foreach ($cartData['items'] as $item) {
                // Insert Item
                $stmtItem = $this->db->prepare("INSERT INTO order_items (order_id, batch_id, quantity, price_at_booking) VALUES (?, ?, ?, ?)");
                $stmtItem->bind_param("iidd", $orderId, $item['id'], $item['cart_qty'], $item['price_per_unit']);
                $stmtItem->execute();

                // Update Batch Qty
                $stmtBatch = $this->db->prepare("UPDATE product_batches SET quantity_available = quantity_available - ? WHERE id = ?");
                $stmtBatch->bind_param("di", $item['cart_qty'], $item['id']);
                $stmtBatch->execute();
            }

            $this->db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Get orders for a user (buyer or farmer)
     */
    public function getByUser($userId, $role)
    {
        if ($role === 'farmer') {
            $sql = "SELECT DISTINCT o.*, u.name as buyer_name 
                    FROM orders o 
                    JOIN order_items oi ON o.id = oi.order_id 
                    JOIN product_batches b ON oi.batch_id = b.id 
                    JOIN products p ON b.product_id = p.id 
                    JOIN users u ON o.buyer_id = u.id
                    WHERE p.farmer_id = ?
                    ORDER BY o.created_at DESC";
        } else {
            $sql = "SELECT o.* FROM orders o WHERE o.buyer_id = ? ORDER BY o.created_at DESC";
        }
        return $this->query($sql, [$userId], "i");
    }

    /**
     * Get statistics for a farmer
     */
    public function getFarmerStats($farmerId)
    {
        // 1. Count active listings
        $sqlListings = "SELECT COUNT(*) as active_listings FROM products WHERE farmer_id = ? AND status = 'published'";
        $activeListings = $this->query($sqlListings, [$farmerId], "i")->fetch_assoc()['active_listings'];

        // 2. Count incoming orders (unique orders containing farmer's products)
        $sqlOrders = "SELECT COUNT(DISTINCT o.id) as incoming_orders 
                      FROM orders o 
                      JOIN order_items oi ON o.id = oi.order_id 
                      JOIN product_batches b ON oi.batch_id = b.id 
                      JOIN products p ON b.product_id = p.id 
                      WHERE p.farmer_id = ?";
        $incomingOrders = $this->query($sqlOrders, [$farmerId], "i")->fetch_assoc()['incoming_orders'];

        return [
            'active_listings' => $activeListings,
            'incoming_orders' => $incomingOrders,
            'harvest_ready' => 0 // Placeholder for now
        ];
    }
}
?>