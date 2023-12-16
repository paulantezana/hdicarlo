<?php

use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/../src/Core/Model.php');

class ModelTest extends TestCase
{
    protected static $db;
    protected static $model;

    public static function setUpBeforeClass(): void
    {
        // Configurar la conexión PDO y la instancia del modelo para las pruebas
        $dsn = 'mysql:host=localhost;dbname=hdicarlo;charset=utf8';
        $username = 'root';
        $password = 'admin';
        self::$db = new PDO($dsn, $username, $password);

        // Asegurarse de que los errores de PDO generen excepciones
        self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Configurar el nombre de la tabla y el ID de la tabla para las pruebas
        $table = 'test_table';
        $tableID = 'id';

        self::$model = new Model($table, $tableID, self::$db);

        // Crear la tabla de prueba
        self::$db->exec("
            CREATE TABLE IF NOT EXISTS $table (
                $tableID INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                age INT NOT NULL
            )
        ");
    }

    public static function tearDownAfterClass(): void
    {
        // Eliminar la tabla de prueba después de todas las pruebas
        self::$db->exec("DROP TABLE IF EXISTS test_table");
    }

    public function testGetAll()
    {
        // Agregar datos de prueba a la tabla
        self::$db->exec("INSERT INTO test_table (name, age) VALUES ('Juan mamani', 25), ('Ana sumire', 30)");

        // Obtener todos los datos y verificar la estructura del resultado
        $result = self::$model->getAll();
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertArrayHasKey('age', $result[0]);
    }

    public function testSearchBy()
    {
        // Agregar datos de prueba a la tabla
        self::$db->exec("INSERT INTO test_table (name, age) VALUES ('Jorge', 25), ('Sonia', 30)");

        // Buscar por nombre y verificar la estructura del resultado
        $result = self::$model->searchBy('name', 'Jorge');
        $this->assertCount(1, $result);
        $this->assertEquals('Jorge', $result[0]['name']);
        $this->assertEquals(25, $result[0]['age']);
    }

    public function testUpdateById()
    {
        // Agregar datos de prueba a la tabla
        self::$db->exec("INSERT INTO test_table (name, age) VALUES ('Ricardo', 25)");
        $lastId = self::$db->lastInsertId();

        // Datos actualizados para la prueba
        $updatedData = ['name' => 'Maria', 'age' => 30];
        $resultId = self::$model->updateById($lastId, $updatedData);

        // Verificar que el ID devuelto es el mismo que el ID del registro actualizado
        $this->assertEquals($lastId, $resultId);

        // Obtener el registro actualizado y verificar los datos
        $updatedRecord = self::$model->getById($lastId);
        $this->assertEquals($updatedData['name'], $updatedRecord['name']);
        $this->assertEquals($updatedData['age'], $updatedRecord['age']);
    }
}
