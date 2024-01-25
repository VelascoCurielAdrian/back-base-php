CREATE TABLE categoria_producto (
  id serial PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL,
  descripcion VARCHAR(250) NOT NULL,
  estatus BOOLEAN NOT NULL DEFAULT TRUE,
  activo BOOLEAN NOT NULL DEFAULT TRUE,
  fechaRegistro TIMESTAMP NOT NULL DEFAULT NOW(),
  fechaModificacion TIMESTAMP
);

CREATE TABLE producto (
  id serial PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL,
  descripcion VARCHAR(250) NOT NULL,
  precio DECIMAL(10, 2) NOT NULL,
  categoria_id INT NOT NULL,
  estatus BOOLEAN NOT NULL DEFAULT TRUE,
  activo BOOLEAN NOT NULL DEFAULT TRUE,
  fechaRegistro TIMESTAMP NOT NULL DEFAULT NOW(),
  fechaModificacion TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categoria_producto(id)
);

CREATE TABLE usuario (
  id serial PRIMARY KEY,
  nombre_completo VARCHAR(50) NOT NULL,
  usuario VARCHAR(250) NOT NULL,
  password VARCHAR(250) NOT NULL,
  estatus BOOLEAN NOT NULL DEFAULT TRUE,
  activo BOOLEAN NOT NULL DEFAULT TRUE,
  fechaRegistro TIMESTAMP NOT NULL DEFAULT NOW(),
  fechaModificacion TIMESTAMP
);

--FUNCIÓN PARA CREAR UNA CATEGORIA PRODUCTO
--==========================================================
CREATE
OR REPLACE FUNCTION crear_categoria(
  nombre VARCHAR(100),
  descripcion VARCHAR(250)
) RETURNS INTEGER AS $$ DECLARE nuevo_id INTEGER;

BEGIN
INSERT INTO
  categoria_producto (nombre, descripcion)
VALUES
  (nombre, descripcion) RETURNING id INTO nuevo_id;

RETURN nuevo_id;

END;

$$ LANGUAGE plpgsql;

--EJECUTABLE
--==========================================================
SELECT
  crear_categoria('Despensa', 'Enlatados, granos');

SELECT
  crear_categoria('Lacteos', 'Queso, Leche');

SELECT
  crear_categoria('Refrescos', 'Coca, Agua');

--FUNCIÓN PARA ACTUALIZAR UNA CATEGORIA PRODUCTO
--==========================================================
CREATE
OR REPLACE FUNCTION actualizar_categoria(
  nombre VARCHAR(50),
  descripcion VARCHAR(250),
  categoria_id INTEGER,
  estatus BOOLEAN
) RETURNS BOOLEAN AS $$ BEGIN
UPDATE
  categoria_producto
SET
  nombre = actualizar_categoria.nombre,
  descripcion = actualizar_categoria.descripcion,
  estatus = actualizar_categoria.estatus
WHERE
  id = actualizar_categoria.categoria_id;

RETURN FOUND;

END;

$$ LANGUAGE plpgsql;

--EJECUTABLE
--==========================================================
SELECT
  actualizar_categoria(
    'Despensa General',
    'Enlatados, granos, avena',
    1,
    TRUE
  );

--FUNCIÓN PARA CONSULTAR UNA CATEGORIA PRODUCTO
--==========================================================
CREATE
OR REPLACE FUNCTION consultar_categoria(categoria_id INTEGER) RETURNS TABLE (
  id INTEGER,
  nombre VARCHAR(50),
  descripcion VARCHAR(250),
  estatus BOOLEAN
) as $$ BEGIN RETURN QUERY
SELECT
  ct_prd.id,
  ct_prd.nombre,
  ct_prd.descripcion,
  ct_prd.estatus
FROM
  categoria_producto ct_prd
WHERE
  ct_prd.id = categoria_id;

END;

$$ LANGUAGE plpgsql;

--EJECUTABLE
--==========================================================
SELECT
  consultar_categoria(1);

--FUNCIÓN PARA ELIMINAR UNA CATEGORIA PRODUCTO
--==========================================================
CREATE
OR REPLACE FUNCTION eliminar_categoria(categoria_id INTEGER) RETURNS BOOLEAN AS $$ BEGIN
UPDATE
  categoria_producto
SET
  activo = FALSE
WHERE
  id = eliminar_categoria.categoria_id;

RETURN FOUND;

END;

$$ LANGUAGE plpgsql;

--EJECUTABLE
--==========================================================
SELECT
  eliminar_categoria(1);

--FUNCIÓN PARA CREAR UN PRODUCTO
--==========================================================
CREATE
OR REPLACE FUNCTION crear_producto(
  nombre VARCHAR(100),
  descripcion VARCHAR(250),
  precio DECIMAL(10, 2),
  categoria_id INTEGER
) RETURNS INTEGER AS $$ DECLARE nuevo_id INTEGER;

BEGIN
INSERT INTO
  producto (nombre, descripcion, precio, categoria_id)
VALUES
  (nombre, descripcion, precio, categoria_id) RETURNING id INTO nuevo_id;

RETURN nuevo_id;

END;

$$ LANGUAGE plpgsql;

--EJECUTABLE
--==========================================================
SELECT
  crear_producto('Coca cola', 'coca cola 600ml', 20.22, 1);

SELECT
  crear_producto('Jugo', 'jugo valle 600ml', 21.60, 3);

SELECT
  crear_producto('Arroz', 'arroz del valle', 28.20, 2);

--FUNCIÓN PARA ACTUALIZAR UN PRODUCTO
--==========================================================
CREATE
OR REPLACE FUNCTION actualizar_producto(
  nombre VARCHAR(100),
  descripcion VARCHAR(250),
  precio DECIMAL(10, 2),
  categoria_id INTEGER,
  estatus BOOLEAN,
  productoID INTEGER
) RETURNS BOOLEAN AS $$ BEGIN
UPDATE
  producto
SET
  nombre = actualizar_producto.nombre,
  descripcion = actualizar_producto.descripcion,
  precio = actualizar_producto.precio,
  categoria_id = actualizar_producto.categoria_id,
  estatus = actualizar_producto.estatus
WHERE
  id = actualizar_producto.productoID;

RETURN FOUND;

END;

$$ LANGUAGE plpgsql;

--EJECUTABLE
--==========================================================
SELECT
  actualizar_producto('Coca cola', 'coca cola 3LT', 20.22, 1, TRUE, 1);

--FUNCIÓN PARA CONSULTAR UN PRODUCTO POR ID
--==========================================================
CREATE
OR REPLACE FUNCTION consultar_producto(productoID INTEGER) RETURNS TABLE (
  id INTEGER,
  nombre VARCHAR(50),
  descripcion VARCHAR(250),
  precio DECIMAL(10, 2),
  estatus BOOLEAN,
  categoria_id INTEGER
) as $$ BEGIN RETURN QUERY
SELECT
  prd.id,
  prd.nombre,
  prd.descripcion,
  prd.precio,
  prd.estatus,
  prd.categoria_id
FROM
  producto prd
WHERE
  prd.id = productoID;

END;

$$ LANGUAGE plpgsql;

--EJECUTABLE
--==========================================================
SELECT
  consultar_producto(1);

--FUNCIÓN PARA ELIMNAR UN PRODUCTO
--==========================================================
CREATE
OR REPLACE FUNCTION eliminar_producto(categoria_id INTEGER) RETURNS BOOLEAN AS $$ BEGIN
UPDATE
  producto
SET
  activo = FALSE
WHERE
  id = eliminar_producto.categoria_id;

RETURN FOUND;

END;

$$ LANGUAGE plpgsql;

--EJECUTABLE
--==========================================================
SELECT
  eliminar_producto(1);

--FUNCIÓN PARA REGISTRAR UN USUARIO
--==========================================================
CREATE
OR REPLACE FUNCTION registro_usuario(
  nombre_completo VARCHAR(50),
  usuario VARCHAR(250),
  password VARCHAR(250)
) RETURNS INTEGER AS $$ DECLARE nuevo_id INTEGER;

BEGIN
INSERT INTO
  usuario ("nombre_completo", "usuario", "password")
VALUES
  (nombre_completo, usuario, password) RETURNING id INTO nuevo_id;

RETURN nuevo_id;

END;

$$ LANGUAGE plpgsql;

--FUNCIÓN PARA LOGIN DE  UN USUARIO
--==========================================================
CREATE
OR REPLACE FUNCTION login(
  user_usuario VARCHAR(250),
  user_password VARCHAR(250)
) RETURNS TABLE (
  id INTEGER,
  nombre_completo VARCHAR(50),
  usuario VARCHAR(250)
) as $$ BEGIN RETURN QUERY
SELECT
  id,
  nombre_completo,
  usuario
FROM
  usuario
WHERE
  usuario = user_usuario AND password = user_password;

END;

$$ LANGUAGE plpgsql;


/*
 COMANDOS PARA CREAR MIGRACIONES EN PHP CON SPARK
 php spark make:migration Create_Categoria_Producto_Table
 php spark migrate
 
 COMANDOS PARA CREAR CONTROLADORES Y MODELOS EN PHP CON SPARK
 php spark make:controller Productos
 php spark make:model Productos
 */