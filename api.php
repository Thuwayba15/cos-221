<?php
require_once "config.php";
//session_start();

class HoopAPI {
    public static function instance(){
        static $instance = null;
        if ($instance === null) {          
            $instance = new HoopAPI();
        }
        return $instance;
    }


    public function registerUser($dbConnection, $name, $surname, $email, $password, $username, $dateOfBirth, $country, $subscription_plan, $accountNumber){
        //validate input
        $invalid = false;
        $message = "";
        // validate email
        $email_pattern =  "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/";
        if (!preg_match($email_pattern, $email)){
            $invalid = true;
            $message .= "Invalid email\\n";
        }
        // validate name
        if (!preg_match("/^[a-zA-Z]{2,}$/",$name)){
            $invalid = true;
            $message .= "Invalid Name\\n";
        }
        //validate surname
        if (!preg_match("/^[a-zA-Z]{2,}$/",$surname)){
            $invalid = true;
            $message .= "Surname required\\n";
        }
        // validate password
        $password_pattern = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z])(?=.*[^a-zA-Z\d\s])\S{8,}$/";
        if (!preg_match($password_pattern, $password)){
            $invalid = true;
            $message .= "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number and one special character\\n";
        }
        // validate username
        if (!preg_match("/^[a-zA-Z0-9]{2,}$/",$username)){
            $invalid = true;
            $message .= "Invalid username\\n";
        }
        // validate date of birth
        if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$dateOfBirth)){
            $invalid = true;
            $message .= "Invalid date of birth\\n";
        }
        // validate subscription plan
        $valid_plans = ['student', 'individual', 'family'];
        if (!in_array($subscription_plan, $valid_plans)) {
            $invalid = true;
            $message .= "Invalid subscription plan\\n";
        }
        // validate account number
        if (!preg_match("/^[0-9]{8,12}$/",$accountNumber)){ //between 8 and 12 digits
            $invalid = true;
            $message .= "Invalid account number\\n";
        }

        //check if already exists
        if (!$invalid){
            $stmt = $dbConnection->prepare("SELECT * FROM USER WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0){
                $invalid = true;
                $message .= "User already exists\\n";
            }
        }

        if (!$invalid){
            $salt = uniqid(random_int(PHP_INT_MIN,PHP_INT_MAX), true);
            $password = $salt . $password;
            $hashed_password = password_hash($password, PASSWORD_ARGON2I);
            $pass = explode("$", $hashed_password);

            $bytes = random_bytes(20);
            $key = bin2hex($bytes);

            $stmt = $dbConnection->prepare("INSERT INTO users (name, surname, email, password, username, dateofBirth, country, subscription_plan, accountNumber, api_key, salt) VALUES VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssss", $name, $surname, $email, $hashed_password, $username, $dateOfBirth, $country, $subscription_plan, $accountNumber, $key, $salt);
            $result = $stmt->execute();
            if ($result){
                $message .= "User added successfully\\n";
            }
            else {
                $message .= "Error adding user\\n";
                $invalid = true;
            }
        }

        if (!$invalid){
            $status = "success";
        }
        else {
            $status = "error";
        }

        if (!$invalid){
            $data = array("apikey" => $key, "message: " => $message);
            $_SESSION['apikey'] = $key;
            $_SESSION["user"] = $name . " " . $surname;
        }
        else {
            $data = array("message" => "HTTP Error Code 400: " . $message);
        }

        $response = array(
            "status" => $status,
            "timestamp" => time(),
            "data" => $data
        );

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function login($dbConnection, $input)
    {
        $invalid = false;
        $inv_email = false;
        $inv_pass = false;
        $message = "";
        $email = $input["email"];
        $password = $input["password"];
        $stmt = $dbConnection->prepare("SELECT * FROM USERS WHERE email = ?");
        //echo $email . " " . $password;
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0){
            $invalid = true;
            $inv_email = true;
            $message .= "Invalid email\\n";
        }
        else {
            $row = $result->fetch_assoc();
            $salt = $row["salt"];
            $theme = $row["theme"];
            $password = $salt . $password;
            $hashed_password = password_hash($password, PASSWORD_ARGON2I);
            $pass = explode("$", $hashed_password);
            //echo $pass[4] . " " . $row["pass"];
            if (password_verify($password, $row["password"]) == false){
                $invalid = true;
                $inv_pass = true;
                $message .= "Invalid password\\n";
            }
        }

        if (!$invalid){
            //$key = $row["api_key"];
            $data = array("message" => "Login successful");
            //$_SESSION['apikey'] = $key;
            $_SESSION["user"] = $row["name"] . " " . $row["surname"];
            $_SESSION["userID"] = $row["userID"];
        }
        else {
            $data = array("message" => "HTTP Error Code 400: " . $message);
            if ($inv_email){
                $data["type"] = "email";
            }
            else {
                $data["type"] = "password";
            }
        }

        $response = array(
            "status" => $invalid ? "error" : "success",
            "timestamp" => time(),
            "data" => $data
        );

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function getAllContent($dbConnection){
        $invalid = false;
        $message = "";
        $sql = "";
        $types = "";
        $params = [];
        $response = [];

        //code to execute the query and return the results
        if (!$invalid) {
            $sql = "SELECT title, image FROM CONTENT";
            $stmt = $dbConnection->prepare($sql);
            if ($stmt) {
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = [
                        'title' => $row['title'],
                        'image' => $row['image']
                    ];
                }
                $response["status"] = "success";
                $response["timestamp"] = time();
                $response["data"] = $data;
            } else {
                $message = 'Failed to prepare statement';
                $invalid = true;
            }
        }

        if ($invalid) {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: " . $message);
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }
    
    public function searchByTitle($dbConnection, $title){
        $sql = "SELECT title, image FROM CONTENT WHERE ";
        $types = "s";
        $params = [];
        $response = [];

        $search_title = "%".$title."%";
        $sql .= "(title LIKE ?) ";
        $params[] = $search_title;

        $stmt = $dbConnection->prepare($sql);
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image']
                ];
            }
            $response["status"] = "success";
            $response["timestamp"] = time();
            $response["data"] = $data;
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function sortbyTitle ($dbConnection, $input){
        $sql = "SELECT title, image FROM CONTENT ORDER BY title ";
        $types = "";
        $params = [];
        $response = [];

        if ($input["order"] == "ASC" || $input["order"] == "DESC"){
            $sql .= $input["order"];
        }
        else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Invalid order");
            $jsonResponse = json_encode($response);
            header("Content-Type: application/json");
            echo $jsonResponse;
            return;
        }

        $stmt = $dbConnection->prepare($sql);
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image']
                ];
            }
            $response["status"] = "success";
            $response["timestamp"] = time();
            $response["data"] = $data;
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function sortbyDate($dbConnection, $input){
        $sql = "SELECT title, image FROM CONTENT ORDER BY releaseDate ";
        $types = "";
        $params = [];
        $invalid = false;
        $message = "";
        $response = [];

        if ($input["order"] == "ASC" || $input["order"] == "DESC"){
            $sql .= $input["order"];
        }
        else {
            $invalid = true;
            $message = 'Invalid order';
        }

        if (!$invalid) {
            $stmt = $dbConnection->prepare($sql);
            if ($stmt) {
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = [
                        'title' => $row['title'],
                        'image' => $row['image']
                    ];
                }
                $response["status"] = "success";
                $response["timestamp"] = time();
                $response["data"] = $data;
            } else {
                $invalid = true;
                $message = 'Failed to prepare statement';
            }
        }

        if ($invalid) {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: " . $message);
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function sortByRating($dbConnection){ //no input cause only descending order
        $sql = "SELECT title, image FROM CONTENT ORDER BY rating DESC";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image']
                ];
            }
            $response["status"] = "success";
            $response["timestamp"] = time();
            $response["data"] = $data;
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function filterByGenre($dbConnection, $genre){
        $sql = "SELECT title, image FROM CONTENT WHERE genre = ?";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->bind_param("s", $genre);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image']
                ];
            }
            $response["status"] = "success";
            $response["timestamp"] = time();
            $response["data"] = $data;
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function filterByReleaseYear($dbConnection, $year){
        $sql = "SELECT title, image FROM CONTENT WHERE YEAR(releaseDate) = ?";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->bind_param("i", $year);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image']
                ];
            }
            $response["status"] = "success";
            $response["timestamp"] = time();
            $response["data"] = $data;
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function filterByLanguage($dbConnection, $language){
        $sql = "SELECT title, image FROM CONTENT WHERE language = ?";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->bind_param("s", $language);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image']
                ];
            }
            $response["status"] = "success";
            $response["timestamp"] = time();
            $response["data"] = $data;
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function getAllMovies($dbConnection){
        $sql = "SELECT title, image FROM CONTENT AS c JOIN MOVIES AS m ON c.contentID = m.contentID";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image']
                ];
            }
            $response["status"] = "success";
            $response["timestamp"] = time();
            $response["data"] = $data;
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function getAllSeries($dbConnection){
        $sql = "SELECT title, image FROM CONTENT AS c JOIN TV_SERIES AS tv ON c.contentID = tv.contentID";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image']
                ];
            }
            $response["status"] = "success";
            $response["timestamp"] = time();
            $response["data"] = $data;
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }
}

//get input
$json = file_get_contents("php://input");
$data = json_decode($json, true);

if ($data["type"] == "registerUser"){
    $user = HoopAPI::instance();
    $user->registerUser($dbConnection, $data["name"], $data["surname"], $data["email"], $data["password"], $data["username"], $data["dateOfBirth"], $data["country"], $data["subscription_plan"], $data["accountNumber"]);
}

if ($data["type"] == "getContent"){
    //echo $json;
    $listings = HoopAPI::instance();
    $listings->getAllContent($dbConnection);
} 

if ($data["type"] == "login"){
    //echo "hello";
    $login = HoopAPI::instance();
    $login->login($dbConnection, $data);
}

if ($data["type"] == "searchByTitle"){
    $search = HoopAPI::instance();
    $search->searchByTitle($dbConnection, $data["title"]);
}

if ($data["type"] == "sortByTitle"){
    $sort = HoopAPI::instance();
    $sort->sortbyTitle($dbConnection, $data);
}

if ($data["type"] == "sortByDate"){
    $sort = HoopAPI::instance();
    $sort->sortbyDate($dbConnection, $data);
}

if ($data["type"] == "sortByRating"){
    $sort = HoopAPI::instance();
    $sort->sortByRating($dbConnection);
}

if ($data["type"] == "filterByGenre"){
    $filter = HoopAPI::instance();
    $filter->filterByGenre($dbConnection, $data["genre"]);
}

if ($data["type"] == "filterByReleaseYear"){
    $filter = HoopAPI::instance();
    $filter->filterByReleaseYear($dbConnection, $data["year"]);
}

if ($data["type"] == "filterByLanguage"){
    $filter = HoopAPI::instance();
    $filter->filterByLanguage($dbConnection, $data["language"]);
}

if ($data["type"] == "getAllMovies"){
    $movies = HoopAPI::instance();
    $movies->getAllMovies($dbConnection);
}

if ($data["type"] == "getAllSeries"){
    $series = HoopAPI::instance();
    $series->getAllSeries($dbConnection);
}

$dbConnection->close();
?>