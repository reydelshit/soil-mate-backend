<?php

include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['soil'])) {
            $soil = $_GET['soil'];
            $query = "SELECT * FROM plants WHERE plants.soilMoistureMin <= :soil AND plants.soilMoistureMax >= :soil";
        } else {
            $query = "SELECT * FROM plants";
        }
        
        $stmt = $conn->prepare($query);
        
        if (isset($soil)) {
            $stmt->bindParam(':soil', $soil);
        }
        
        $stmt->execute();
        $plants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($plants);


        break;

    case "POST":
        $data = json_decode(file_get_contents('php://input'));

        $query = "INSERT INTO plants (plant_name, plant_image, plant_description, npk, phLevelMin, phLevelMax, soilMoistureMin, soilMoistureMax, temperatureMin, temperatureMax) VALUES (:plant_name, :plant_image, :plant_description, :npk, :phLevelMin, :phLevelMax, :soilMoistureMin, :soilMoistureMax, :temperatureMin, :temperatureMax)";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':plant_name', $data->plant_name);
        $stmt->bindParam(':plant_image', $data->plant_image);
        $stmt->bindParam(':plant_description', $data->plant_description);
        $stmt->bindParam(':npk', $data->npk);
        $stmt->bindParam(':phLevelMin', $data->phLevelMin);
        $stmt->bindParam(':phLevelMax', $data->phLevelMax);
        $stmt->bindParam(':soilMoistureMin', $data->soilMoistureMin);
        $stmt->bindParam(':soilMoistureMax', $data->soilMoistureMax);
        $stmt->bindParam(':temperatureMin', $data->temperatureMin);
        $stmt->bindParam(':temperatureMax', $data->temperatureMax);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Successfully added"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to add"
            ];
        }

        echo json_encode($response);
        break;


    case "DELETE":
        $sql = "DELETE FROM plants WHERE plant_id = :id";
        $stud = json_decode(file_get_contents('php://input'));


        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $stud->id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "plant deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "plant deletion failed"
            ];
        }
}
