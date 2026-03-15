<?php
namespace Models;

use Core\Model;

class Product extends Model
{
    /**
     * Get products by farmer ID
     */
    public function getByFarmer($farmerId)
    {
        $sql = "SELECT p.*, c.name as category_name, 
                (SELECT MIN(harvest_date) FROM product_batches WHERE product_id = p.id) as earliest_harvest,
                i.image_path
                FROM products p 
                JOIN categories c ON p.category_id = c.id
                LEFT JOIN product_images i ON p.id = i.product_id AND i.is_primary = 1
                WHERE p.farmer_id = ? 
                ORDER BY p.created_at DESC";
        return $this->query($sql, [$farmerId], "i");
    }

    /**
     * Create a new product
     */
    public function create($data)
    {
        $sql = "INSERT INTO products (farmer_id, category_id, name, description, price_per_unit, image, unit, cultivation_method, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $this->query($sql, [
            $data['farmer_id'],
            $data['category_id'],
            $data['name'],
            $data['description'],
            $data['price_per_unit'],
            $data['unit'],
            $data['cultivation_method'],
            $data['status']
            $data['image'],
        ], "iissdsss");

        return $this->db->insert_id;
    }

    /**
     * Add image to product
     */
    public function addImage($productId, $path, $isPrimary = 0)
    {
        $sql = "INSERT INTO product_images (product_id, image_path, is_primary) VALUES (?, ?, ?)";
        return $this->query($sql, [$productId, $path, $isPrimary], "isi");
    }

    /**
     * Add a batch to a product
     */
    public function addBatch($data)
    {
        $sql = "INSERT INTO product_batches (product_id, batch_name, quantity_available, harvest_date, booking_deadline, quality_grade) 
                VALUES (?, ?, ?, ?, ?, ?)";
        return $this->query($sql, [
            $data['product_id'],
            $data['batch_name'],
            $data['quantity'],
            $data['harvest_date'],
            $data['booking_deadline'],
            $data['quality_grade']
        ], "isdsss");
    }

    /**
     * Get latest published products for home page
     */
    public function getLatest($limit = 6)
    {
        $sql = "SELECT p.*, c.name as category_name, 
                (SELECT MIN(harvest_date) FROM product_batches WHERE product_id = p.id) as earliest_harvest,
                i.image_path as image_path
                FROM products p 
                JOIN categories c ON p.category_id = c.id
                LEFT JOIN product_images i ON p.id = i.product_id AND i.is_primary = 1
                WHERE p.status = 'published'
                ORDER BY p.created_at DESC 
                LIMIT ?";
        return $this->query($sql, [$limit], "i");
    }

    /**
     * Get reviews for a product
     */
    public function getReviews($productId)
    {
        $sql = "SELECT r.*, u.name as buyer_name, u.profile_pic 
                FROM reviews r 
                JOIN users u ON r.buyer_id = u.id 
                WHERE r.product_id = ? 
                ORDER BY r.created_at DESC";
        return $this->query($sql, [$productId], "i");
    }

    /**
     * Add a review
     */
    public function addReview($data)
    {
        $sql = "INSERT INTO reviews (buyer_id, product_id, rating, comment) VALUES (?, ?, ?, ?)";
        return $this->query($sql, [
            $data['buyer_id'],
            $data['product_id'],
            $data['rating'],
            $data['comment']
        ], "iiis");
    }

    /**
     * Get average rating
     */
    public function getAverageRating($productId)
    {
        $sql = "SELECT AVG(rating) as average, COUNT(*) as total FROM reviews WHERE product_id = ?";
        return $this->query($sql, [$productId], "i")->fetch_assoc();
    }

    /**
     * Get all categories
     */
    public function getCategories()
    {
        return $this->query("SELECT * FROM categories ORDER BY name ASC");
    }
}
?>