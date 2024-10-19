<?php

namespace Repository;

require_once "./libs/mysql.php";
require_once "./domains/products.php";

use Domain\IProductRepository;
use Domain\Product;
use Exception;
use Libs\MySQL;
use PDO;
use DateTime;

class ProductRepository implements IProductRepository
{
  private readonly PDO $conn;

  public function __construct(MySQL $mySQL)
  {
    $this->conn = $mySQL->getConn();
  }

  public function createProduct(Product $product): void
  {
    try {
      $stmt = $this->conn
        ->prepare("INSERT INTO products (id, type_id, name, description, price, quantity, created_at, updated_at)
        VALUES (:id, :type_id, :name, :description, :price, :quantity, NOW(), NOW())");

      $stmt->execute([
        ":id" => $product->id,
        ":type_id" => $product->typeId,
        ":name" => $product->name,
        ":description" => $product->description,
        ":price" => $product->price,
        ":quantity" => $product->quantity,
      ]);
    } catch (Exception $e) {
      die("Failed to create products to MySQL: " . $e->getMessage());
    }
  }

  public function getProductById(string $id): Product | null
  {
    try {
      $stmt = $this->conn
        ->prepare("SELECT products.* FROM products WHERE id = ?");
      $stmt->execute([$id]);

      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$result) {
        return null;
      }

      $product = new Product();

      $product->id = $result["id"] ?? "";
      $product->typeId = $result["type_id"] ?? "";
      $product->name = $result["name"] ?? "";
      $product->description = $result["description"] ?? "";
      $product->quantity = $result["quantity"] ?? "";
      $product->createdAt = $result["created_at"] ? new DateTime($result["created_at"]) : new DateTime("now");
      $product->updatedAt = $result["updated_at"] ? new DateTime($result["updated_at"]) : new DateTime("now");

      return $product;
    } catch (Exception $e) {
      die("Failed to get product from MySQL: " . $e->getMessage());
    }
  }
}
