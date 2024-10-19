<?php

namespace Repository;

require_once "./domains/product_type.php";
require_once "./libs/mysql.php";

use Domain\IProductTypeRepository;
use Domain\ProductType;
use Libs\MySQL;
use PDO;
use Exception;

class ProductTypeRepository implements IProductTypeRepository
{
  private readonly PDO $conn;

  public function __construct(MySQL $mySQL)
  {
    $this->conn = $mySQL->getConn();
  }

  public function createProductType(ProductType $productType)
  {
    try {
      $stmt = $this->conn
        ->prepare("INSERT INTO product_types (id, name, description, created_at, updated_at) VALUES (:id, :name, :description, NOW(), NOW())");
      $stmt->execute([
        ":id" => $productType->id,
        ":name" => $productType->name,
        ":description" => $productType->description
      ]);
    } catch (Exception $e) {
      die("Failed to create product_types: " . $e->getMessage());
    }
  }
}
