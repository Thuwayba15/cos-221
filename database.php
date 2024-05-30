<?php
require_once "221config.php";

class databaseAPI{
    public static function instance(){
        static $instance = null;
        if ($instance === null) {          
            $instance = new databaseAPI();
        }
        return $instance;
    }

    public function addTV($conn, $input){
        $sql = "INSERT INTO CONTENT (title, language, genre, releaseDate, rating, productionStudio, image, summary, runtime) VALUES ('".$input["title"]."', '".$input["language"]."', '".$input["genre"]."', '".$input["releaseDate"]."', '".$input["rating"]."', '".$input["productionStudio"]."', '".$input["image"]."', '".$input["summary"]."', '".$input["runtime"]."')";
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully  <br>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        //get id of the last inserted content title to use as foreign key
        $contentID = $conn->insert_id;
        $sql = "INSERT INTO TV_SERIES (contentID, status, seasons) VALUES ('".$contentID."', '".$input["status"]."', '".$input["seasons"]."')";
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully <br>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        echo count($input["crew"]) . "<br>";
        for ($i = 0 ; $i < count($input["crew"]) ; $i++){
            //check if the crew member already exists in the database
            $sql = "SELECT * FROM CREW WHERE name = '".$input["crew"][$i]["name"]."'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $crewID = $row["crewID"];
            } else {
                $sql = "INSERT IGNORE INTO CREW (name, birthday, deathday, country, image) VALUES ('".$input["crew"][$i]["name"]."', '".$input["crew"][$i]["birthday"]."', '".$input["crew"][$i]["deathday"]."', '".$input["crew"][$i]["country"]."', '".$input["crew"][$i]["image"]."')";
                echo $sql . "<br>";
                if ($conn->query($sql) === TRUE) {
                    echo "New record created successfully <br>";
                    $crewID = $conn->insert_id;
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }

            $sql = "INSERT IGNORE INTO CREW_ROLE (crewID, role) VALUES ('".$crewID."', '".$input["crew"][$i]["role"]."')";
            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully <br>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            $sql = "INSERT IGNORE INTO WORKS_ON (contentID, crewID) VALUES ('".$contentID."', '".$crewID."')";
            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully <br>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    public function addMovie($conn, $input){
        $sql = "INSERT IGNORE INTO CONTENT (title, language, genre, releaseDate, rating, productionStudio, image, summary, runtime) VALUES ('".$input["title"]."', '".$input["language"]."', '".$input["genre"]."', '".$input["releaseDate"]."', '".$input["rating"]."', '".$input["productionStudio"]."', '".$input["image"]."', '".$input["summary"]."', '".$input["runtime"]."')";
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully  <br>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        $contentID = $conn->insert_id;
        $sql = "INSERT INTO MOVIES (contentID, awards, age_rating, box_office) VALUES ('".$contentID."', '".$input["awards"]."', '".$input["age_rating"]."', '".$input["box_office"]."')";
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully <br>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        for ($i = 0 ; $i < count($input["crew"]) ; $i++){
            $sql = "SELECT * FROM CREW WHERE name = '".$input["crew"][$i]["name"]."'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $crewID = $row["crewID"];
            } else {
                $sql = "INSERT IGNORE INTO CREW (name, birthday, deathday, country, image) VALUES ('".$input["crew"][$i]["name"]."', '".$input["crew"][$i]["birthday"]."', '".$input["crew"][$i]["deathday"]."', '".$input["crew"][$i]["country"]."', '".$input["crew"][$i]["image"]."')";
                echo $sql . "<br>";
                if ($conn->query($sql) === TRUE) {
                    echo "New record created successfully <br>";
                    $crewID = $conn->insert_id;
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }

            $sql = "INSERT IGNORE INTO CREW_ROLE (crewID, role) VALUES ('".$crewID."', '".$input["crew"][$i]["role"]."')";
            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully <br>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            $sql = "INSERT IGNORE INTO WORKS_ON (contentID, crewID) VALUES ('".$contentID."', '".$crewID."')";
            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully <br>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    public function getAllContent($conn){
        //echo "getting all content <br>";
        $sql = "SELECT * FROM CONTENT";
        $result = $conn->query($sql);
        $content = array();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $content[] = $row;
            }
        } else {
            echo "0 results";
        }
        return $content;
    
    }

    public function getUserReviews($dbConnection, $input){
        $userID = $input["userID"];
        $sql = "SELECT contentID, comment, rating FROM REVIEW WHERE userID = ?";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt){
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()){
                $contentID = $row["contentID"];
                $comment = $row["comment"];
                $rating = $row["rating"];
                $sql = "SELECT title FROM CONTENT WHERE contentID = ?";
                $stmt = $dbConnection->prepare($sql);
                if ($stmt){
                    $stmt->bind_param("i", $contentID);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    $title = $row["title"];
                    $data[] = [
                        'title' => $title,
                        'comment' => $comment,
                        'rating' => $rating
                    ];
                }
                else {
                    $response["status"] = "error";
                    $response["timestamp"] = time();
                    $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
                }
            }
            $response["status"] = "success";
            $response["timestamp"] = time();
            $response["data"] = $data;
        }
        else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }
    }
}

$json = file_get_contents("php://input");
$data = json_decode($json, true);
if ($data["type"] == "addTV"){
    databaseAPI::instance()->addTV($conn, $data);
}

else if ($data["type"] == "addMovie"){
    databaseAPI::instance()->addMovie($conn, $data);
}

else if ($data["type"] == "getAllContent"){
    echo json_encode(databaseAPI::instance()->getAllContent($conn));
}

else if ($data["type"] == "getUserReviews"){
    echo json_encode(databaseAPI::instance()->getUserReviews($conn, $data));
}
?>