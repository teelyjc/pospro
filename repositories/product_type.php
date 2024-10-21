<?php

namespace Repository;

require_once "./domains/product_type.php";
require_once "./libs/mysql.php";

use Domain\IProductTypeRepository;
use Domain\ProductType;
use Libs\MySQL;
use PDO;
use Exception;
use DateTime;

class ProductTypeRepository implements IProductTypeRepository
{
  private readonly PDO $conn;

  public function __construct(MySQL $mySQL)
  {
    $this->conn = $mySQL->getConn();
  }

  public function createProductType(ProductType $productType): void
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
      die("Failed to create product_types from MySQL: " . $e->getMessage());
    }
  }

  public function getProductTypeById(string $id): ProductType | null
  {
    try {
      $stmt = $this->conn
        ->prepare("SELECT product_types.* FROM product_types WHERE id = ?");
      $stmt->execute([$id]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$result) {
        return null;
      }

      $productType = new ProductType();

      $productType->id = $result["id"] ?? "";
      $productType->name = $result["name"] ?? "";
      $productType->description = $result["description"] ?? "";
      $productType->createdAt = $result["created_at"] ? new DateTime($result["created_at"]) : new DateTime("now");
      $productType->updatedAt = $result["updated_at"] ? new DateTime($result["updated_at"]) : new DateTime("now");

      return $productType;
    } catch (Exception $e) {
      die("Failed to get product_types from MySQL: " . $e->getMessage());
    }
  }

  /**
   * @return ProductType[]
   */
  public function getProductTypes(): array
  {
    try {
      $productTypes = array();
      $stmt = $this->conn
        ->prepare("SELECT product_types.* FROM product_types");
      $stmt->execute();
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach ($results as $result) {
        $productType = new ProductType();

        $productType->id = $result["id"] ?? "";
        $productType->name = $result["name"] ?? "";
        $productType->description = $result["description"] ?? "";
        $productType->createdAt = $result["created_at"] ? new DateTime($result["created_at"]) : new DateTime("now");
        $productType->updatedAt = $result["updated_at"] ? new DateTime($result["updated_at"]) : new DateTime("now");

        array_push($productTypes, $productType);
      }

      return $productTypes;
    } catch (Exception $e) {
      die("Failed to get product_types from MySQL: " . $e->getMessage());
    }
  }

  public function updateProductTypeById(ProductType $productType): void
  {
    try {
      $stmt = $this->conn
        ->prepare(
          "UPDATE product_types SET name = :name, description = :description, updated_at = NOW() WHERE id = :id"
        );
      $stmt->bindParam(":name", $productType->name, PDO::PARAM_STR);
      $stmt->bindParam(":description", $productType->description, PDO::PARAM_STR);

      $stmt->execute();
    } catch (Exception $e) {
      die("Failed to update product_types to MySQL" . $e->getMessage());
    }
  }
  public function deleteProductTypeById(string $id): void
  {
    try {
      $stmt = $this->conn
        ->prepare(
          "DELETE product_types WHERE id = ?"
        );
      $stmt->execute([$id]);
    } catch (Exception $e) {
      die("Failed to update product_types to MySQL" . $e->getMessage());
    }
  }
}
