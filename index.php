<?php
class DBInfo {
    private $host = 'localhost';
    private $db = 'car_management';
    private $user = 'root'; 
    private $pass = ''; 
    private $conn;

    public function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

        if ($this->conn->connect_error) {
            die("Kết nối thất bại: " . $this->conn->connect_error);
        }

        return $this->conn;
    }
}

class Car {
    public $license_plate;
    public $color;
    public $engine_status;

    public function __construct($license_plate, $color, $engine_status) {
        $this->license_plate = $license_plate;
        $this->color = $color;
        $this->engine_status = $engine_status;
    }

    public function save($conn) {
        $stmt = $conn->prepare("INSERT INTO cars (license_plate, color, engine_status) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $this->license_plate, $this->color, $this->engine_status);
        $stmt->execute();
    }

    public static function getAll($conn) {
        $result = $conn->query("SELECT * FROM cars");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function updateColor($conn, $license_plate, $new_color) {
        $stmt = $conn->prepare("UPDATE cars SET color = ? WHERE license_plate = ?");
        $stmt->bind_param("ss", $new_color, $license_plate);
        $stmt->execute();
    }

    public static function updateEngineStatus($conn, $license_plate, $new_status) {
        $stmt = $conn->prepare("UPDATE cars SET engine_status = ? WHERE license_plate = ?");
        $stmt->bind_param("ss", $new_status, $license_plate);
        $stmt->execute();
    }

    public static function delete($conn, $license_plate) {
        $stmt = $conn->prepare("DELETE FROM cars WHERE license_plate = ?");
        $stmt->bind_param("s", $license_plate);
        $stmt->execute();
    }
}

$dbInfo = new DBInfo();
$conn = $dbInfo->connect();

$x1 = new Car("123", "Đỏ", "Hoạt động");
$x2 = new Car("345", "Vàng", "Hỏng");

$x1->save($conn);
$x2->save($conn);

$cars = Car::getAll($conn);
echo "Danh sách xe:\n";
foreach ($cars as $car) {
    echo "Biển số: {$car['license_plate']}, Màu sơn: {$car['color']}, Tình trạng động cơ: {$car['engine_status']}\n";
}

Car::updateColor($conn, "123", "màu đặc biệt");
Car::updateEngineStatus($conn, "345", "Hoạt động");

Car::delete($conn, "345");

$conn->close();
?>
