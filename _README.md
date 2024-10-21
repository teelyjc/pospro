# Notes

# Bug issues

- ERR_REDIRECT_01
If you are including partials before header() was called. It will break header function.
Because I'm naming a function that's render a header components, default header function was replaced.

# SQL Queries
```sql
-- User's Queries
INSERT INTO users (id, username, password, created_at, updated_at)
  VALUE (:id, :username, :password, NOW(), NOW());

SELECT users.* FROM users WHERE id = ? AND is_deleted = 0;

SELECT users.* FROM users WHERE username = ? AND is_deleted = 0;

UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id;

UPDATE users SET is_deleted = 1, updated_at = NOW() WHERE id = ?;

-- Product's Queries
INSERT INTO products (id, type_id, name, description, price, quantity, created_at, updated_at)
  VALUES (:id, :type_id, :name, :description, :price, :quantity, NOW(), NOW());

SELECT products.* FROM products WHERE id = ?;

SELECT products.* FROM products ORDER BY created_at DESC LIMIT :limit OFFSET :offset;

DELETE FROM products WHERE id = ?;

SELECT COUNT(*) as total FROM products;

UPDATE products
  SET
    name = :name,
    description = :description,
    price = :price,
    quantity = :quantity,
    updated_at = NOW()
  WHERE id = :id;

DELETE products FROM products
  JOIN product_types
    ON products.type_id = product_types.id
  WHERE product_types.id = ?;

-- Product's Type Queries
INSERT INTO product_types (id, name, description, created_at, updated_at)
  VALUES (:id, :name, :description, NOW(), NOW());

SELECT product_types.* FROM product_types WHERE id = ?;

SELECT product_types.* FROM product_types;

UPDATE product_types
  SET
    name = :name,
    description = :description,
    updated_at = NOW()
  WHERE id = :id;

DELETE product_types FROM product_types WHERE id = ?;

SELECT COUNT(*) as count
  FROM products
  JOIN product_types
    ON products.type_id = product_types.id
  WHERE product_types.id = :id
```