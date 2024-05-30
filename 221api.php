<?php
require_once "221config.php";
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
        //echo "helllllooooo";
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
            $stmt = $dbConnection->prepare("SELECT * FROM USERS WHERE email = ?");
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

            $stmt = $dbConnection->prepare("INSERT INTO USERS (username, name, surname, email, password, dateofBirth, country, subscription_plan, accountNumber, api_key, salt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssss", $username, $name, $surname, $email, $hashed_password, $dateOfBirth, $country, $subscription_plan, $accountNumber, $key, $salt);
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
        //echo "tonyyyyy";
        $data = [];
        $invalid = false;
        $message = "";
        $sql = "";
        $types = "";
        $params = [];
        $response = [];

        //code to execute the query and return the results
        if (!$invalid) {
            $sql = "SELECT title, image, contentID FROM CONTENT";
            $stmt = $dbConnection->prepare($sql);
            if ($stmt) {
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = [
                        'title' => $row['title'],
                        'image' => $row['image'], 
                        'contentID' => $row['contentID']
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
        $sql = "SELECT title, image, contentID FROM CONTENT WHERE ";
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
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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
        $sql = "SELECT title, image, contentID FROM CONTENT ORDER BY title ";
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
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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
        $sql = "SELECT title, image, contentID FROM CONTENT ORDER BY releaseDate ";
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
                        'image' => $row['image'],
                        'contentID' => $row['contentID']
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
        $sql = "SELECT title, image, contentID FROM CONTENT ORDER BY rating DESC";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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
        $sql = "SELECT title, image, contentID FROM CONTENT WHERE genre = ?";
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
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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
        $sql = "SELECT title, image, contentID FROM CONTENT WHERE YEAR(releaseDate) = ?";
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
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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
        $sql = "SELECT title, image, contentID FROM CONTENT WHERE language = ?";
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
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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
        $sql = "SELECT title, image, c.contentID FROM CONTENT AS c JOIN MOVIES AS m ON c.contentID = m.contentID";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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
        $sql = "SELECT title, image, c.contentID FROM CONTENT AS c JOIN TV_SERIES AS tv ON c.contentID = tv.contentID";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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

    public function getWatchList($dbConnection, $userID){
        $sql = "SELECT c.title, c.image, c.contentID FROM CONTENT AS c INNER JOIN INTERACTS AS i ON c.contentID = i.contentID WHERE i.userID = ? AND watchlist = 1";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->bind_param("i", $userID);
            $completed = $stmt->execute();
            if ($completed) {
                $result = $stmt->get_result();
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = [
                        'title' => $row['title'],
                        'image' => $row['image'],
                        'contentID' => $row['contentID']
                    ];
                }
                $response["status"] = "success";
                $response["timestamp"] = time();
                $response["data"] = $data;
            } else {
                $response["status"] = "error";
                $response["timestamp"] = time();
                $response["data"] = array("message" => "HTTP Error Code 400: Failed to execute statement");
            }
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function getLiked($dbConnection, $userID){
        $sql = "SELECT c.title, c.image, c.contentID FROM CONTENT AS c INNER JOIN INTERACTS AS i ON c.contentID = i.contentID WHERE i.userID = ? AND liked = 1";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->bind_param("i", $userID);
            $completed = $stmt->execute();
            if ($completed) {
                $result = $stmt->get_result();
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = [
                        'title' => $row['title'],
                        'image' => $row['image'],
                        'contentID' => $row['contentID']
                    ];
                }
                $response["status"] = "success";
                $response["timestamp"] = time();
                $response["data"] = $data;
            } else {
                $response["status"] = "error";
                $response["timestamp"] = time();
                $response["data"] = array("message" => "HTTP Error Code 400: Failed to execute statement");
            }
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function recommended($dbConnection, $userID){
        $sqlGenre = "SELECT genre FROM CONTENT AS c INNER JOIN INTERACTS as i on c.contentID = i.contentID WHERE i.userID = ? GROUP BY c.genre ORDER BY COUNT(*) DESC LIMIT 1";
        $stmtGenre = $dbConnection->prepare($sqlGenre);
        $response = [];

        if ($stmtGenre){
            $stmtGenre->bind_param("i", $userID);
            $stmtGenre->execute();
            $resultGenre = $stmtGenre->get_result();
            $genre = $resultGenre->fetch_assoc();
            $genre = $genre["genre"];
            $sqlContent = "SELECT title, image, contentID FROM CONTENT WHERE genre = ? LIMIT 10";
            $stmtContent = $dbConnection->prepare($sqlContent);
            if ($stmtContent) {
                $stmtContent->bind_param("s", $genre);
                $stmtContent->execute();
                $resultContent = $stmtContent->get_result();
                $data = [];
                while ($row = $resultContent->fetch_assoc()) {
                    $data[] = [
                        'title' => $row['title'],
                        'image' => $row['image'],
                        'contentID' => $row['contentID']
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
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function addMovie($dbConnection, $title, $language, $genre, $releaseDate, $rating, $productionStudio, $image, $summary, $runtime, $awards, $age_rating, $box_office){
        $sql = "INSERT INTO CONTENT (title, language, genre, releaseDate, rating, productionStudio, image, summary, runtime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt){
            $stmt->bind_param("ssssdsssi", $title, $language, $genre, $releaseDate, $rating, $productionStudio, $image, $summary, $runtime);
            $completed = $stmt->execute();
            if ($completed){
                $contentID = $stmt->insert_id;
                $sql = "INSERT INTO MOVIES (contentID, awards, age_rating, box_office) VALUES (?, ?, ?, ?)";
                $stmt = $dbConnection->prepare($sql);
                if ($stmt){
                    $stmt->bind_param("issd", $contentID, $awards, $age_rating, $box_office);
                    $completed = $stmt->execute();
                    if ($completed){
                        $response["status"] = "success";
                        $response["timestamp"] = time();
                        $response["data"] = array("message" => "Movie added successfully");
                    } else {
                        $response["status"] = "error";
                        $response["timestamp"] = time();
                        $response["data"] = array("message" => "HTTP Error Code 400: Failed to execute statement");
                    }
                } else {
                    $response["status"] = "error";
                    $response["timestamp"] = time();
                    $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
                }
            } else {
                $response["status"] = "error";
                $response["timestamp"] = time();
                $response["data"] = array("message" => "HTTP Error Code 400: Failed to execute statement");
            }
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function addSeries($dbConnection, $title, $language, $genre, $releaseDate, $rating, $productionStudio, $image, $summary, $runtime, $status, $seasons){
        $sqlContent = "INSERT INTO CONTENT (title, language, genre, releaseDate, rating, productionStudio, image, summary, runtime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtContent = $dbConnection->prepare($sqlContent);
        $response = [];

        if ($stmtContent){
            $stmtContent->bind_param("ssssdsssi", $title, $language, $genre, $releaseDate, $rating, $productionStudio, $image, $summary, $runtime);
            $completedContent = $stmtContent->execute();
            if ($completedContent){
                $contentID = $stmtContent->insert_id;
                $sqlSeries = "INSERT INTO TV_SERIES (contentID, status, seasons) VALUES (?, ?, ?)";
                $stmtSeries = $dbConnection->prepare($sqlSeries);
                if ($stmtSeries){
                    $stmtSeries->bind_param("isi", $contentID, $status, $seasons);
                    $completedSeries = $stmtSeries->execute();
                    if ($completedSeries){
                        $response["status"] = "success";
                        $response["timestamp"] = time();
                        $response["data"] = array("message" => "Series added successfully");
                    } else {
                        $response["status"] = "error";
                        $response["timestamp"] = time();
                        $response["data"] = array("message" => "HTTP Error Code 400: Failed to execute statement");
                    }
                } else {
                    $response["status"] = "error";
                    $response["timestamp"] = time();
                    $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
                }
            } else {
                $response["status"] = "error";
                $response["timestamp"] = time();
                $response["data"] = array("message" => "HTTP Error Code 400: Failed to execute statement");
            }
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function sortTVByTitle($dbConnection){
        $sql = "SELECT title, image, c.contentID FROM CONTENT AS c INNER JOIN TV_SERIES AS tv ON c.contentID = tv.contentID ORDER BY title";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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

    public function sortMovieByTitle($dbConnection){
        $sql = "SELECT title, image, c.contentID FROM CONTENT AS c INNER JOIN MOVIES AS m ON c.contentID = m.contentID ORDER BY title";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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

    public function sortTVByDate($dbConnection){
        $sql = "SELECT title, image, c.contentID FROM CONTENT AS c INNER JOIN TV_SERIES AS tv ON c.contentID = tv.contentID ORDER BY releaseDate";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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

    public function sortMovieByDate($dbConnection){
        $sql = "SELECT title, image, c.contentID FROM CONTENT AS c INNER JOIN MOVIES AS m ON c.contentID = m.contentID ORDER BY releaseDate";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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

    public function sortTVByRating($dbConnection){
        $sql = "SELECT title, image, c.contentID FROM CONTENT AS c INNER JOIN TV_SERIES AS tv ON c.contentID = tv.contentID ORDER BY rating DESC";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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

    public function sortMovieByRating($dbConnection){
        $sql = "SELECT title, image, c.contentID FROM CONTENT AS c INNER JOIN MOVIES AS m ON c.contentID = m.contentID ORDER BY rating DESC";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'title' => $row['title'],
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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

    public function filterTVByGenre($dbConnection, $genre){
        $sql = "SELECT title, image, c.contentID FROM CONTENT AS c INNER JOIN TV_SERIES AS tv ON c.contentID = tv.contentID WHERE genre = ?";
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
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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

    public function filterMovieByGenre($dbConnection, $genre){
        $sql = "SELECT title, image, c.contentID FROM CONTENT AS c INNER JOIN MOVIES AS m ON c.contentID = m.contentID WHERE genre = ?";
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
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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

    public function filterTVByYear($dbConnection, $year){
        $sql = "SELECT title, image, c.contentID FROM CONTENT AS c INNER JOIN TV_SERIES AS tv ON c.contentID = tv.contentID WHERE YEAR(releaseDate) = ?";
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
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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

    public function filterMovieByYear($dbConnection, $year){
        $sql = "SELECT title, image, c.contentID FROM CONTENT AS c INNER JOIN MOVIES AS m ON c.contentID = m.contentID WHERE YEAR(releaseDate) = ?";
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
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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

    public function filterTVByLanguage($dbConnection, $language){
        $sql = "SELECT title, image, c.contentID FROM CONTENT AS c INNER JOIN TV_SERIES AS tv ON c.contentID = tv.contentID WHERE language = ?";
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
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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

    public function filterMovieByLanguage($dbConnection, $language){
        $sql = "SELECT title, image, c.contentID FROM CONTENT AS c INNER JOIN MOVIES AS m ON c.contentID = m.contentID WHERE language = ?";
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
                    'image' => $row['image'],
                    'contentID' => $row['contentID']
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

    public function addToWatchList($dbConnection, $userID, $contentID){
        $sql = "INSERT INTO INTERACTS (userID, contentID, watchlist) VALUES (?, ?, 1)";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->bind_param("ii", $userID, $contentID);
            $completed = $stmt->execute();
            if ($completed) {
                $response["status"] = "success";
                $response["timestamp"] = time();
                $response["data"] = array("message" => "Added to watchlist successfully");
            } else {
                $response["status"] = "error";
                $response["timestamp"] = time();
                $response["data"] = array("message" => "HTTP Error Code 400: Failed to execute statement");
            }
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function addToLiked($dbConnection, $userID, $contentID){
        $sql = "INSERT INTO INTERACTS (userID, contentID, liked) VALUES (?, ?, 1)";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->bind_param("ii", $userID, $contentID);
            $completed = $stmt->execute();
            if ($completed) {
                $response["status"] = "success";
                $response["timestamp"] = time();
                $response["data"] = array("message" => "Added to liked successfully");
            } else {
                $response["status"] = "error";
                $response["timestamp"] = time();
                $response["data"] = array("message" => "HTTP Error Code 400: Failed to execute statement");
            }
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function removeFromWatchList($dbConnection, $userID, $contentID){
        $sql = "UPDATE INTERACTS SET watchlist=0 WHERE userID = ? AND contentID = ?";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt) {
            $stmt->bind_param("ii", $userID, $contentID);
            $completed = $stmt->execute();
            if ($completed) {
                $response["status"] = "success";
                $response["timestamp"] = time();
                $response["data"] = array("message" => "Removed from watchlist successfully");
            } else {
                $response["status"] = "error";
                $response["timestamp"] = time();
                $response["data"] = array("message" => "HTTP Error Code 400: Failed to execute statement");
            }
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function moreContentInfo($dbConnection, $contentID){
        $sql1 = "SELECT * FROM CONTENT WHERE contentID = ?";
        $stmt1 = $dbConnection->prepare($sql1);
        $response = [];

        if ($stmt1) {
            $stmt1->bind_param("i", $contentID);
            $stmt1->execute();
            $result1 = $stmt1->get_result();
            $data = [];
            while ($row = $result1->fetch_assoc()) {
                $data = [
                    'title' => $row['title'],
                    'language' => $row['language'],
                    'genre' => $row['genre'],
                    'releaseDate' => $row['releaseDate'],
                    'rating' => $row['rating'],
                    'productionStudio' => $row['productionStudio'],
                    'image' => $row['image'],
                    'summary' => $row['summary'],
                    'runtime' => $row['runtime']
                ];
            }
            $sql2 = "SELECT * FROM MOVIES WHERE contentID = ?";
            $stmt2 = $dbConnection->prepare($sql2);
            if ($stmt2) {
                $stmt2->bind_param("i", $contentID);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                if ($result2->num_rows > 0) {
                    $row = $result2->fetch_assoc();
                    $data = array_merge($data, [
                        'awards' => $row['awards'],
                        'age_rating' => $row['age_rating'],
                        'box_office' => $row['box_office']
                    ]);
                }
                else {
                    $sql3 = "SELECT * FROM TV_SERIES WHERE contentID = ?";
                    $stmt3 = $dbConnection->prepare($sql3);
                    if ($stmt3) {
                        $stmt3->bind_param("i", $contentID);
                        $stmt3->execute();
                        $result3 = $stmt3->get_result();
                        if ($result3->num_rows > 0) {
                            $row = $result3->fetch_assoc();
                            $data = array_merge($data, [
                                'status' => $row['status'],
                                'seasons' => $row['seasons']
                            ]);
                        }
                    }
                }
                $response["status"] = "success";
                $response["timestamp"] = time();
                $response["data"] = $data;
            } else {
                $response["status"] = "error";
                $response["timestamp"] = time();
                $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
            }
        } else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function getCrew($dbConnection, $contentID){
        $sql = "SELECT crewID FROM WORKS_ON WHERE contentID = ?";
        $stmt = $dbConnection->prepare($sql);
        $response = [];
        $actors = [];
        $directors = [];
        $writers = [];

        if ($stmt){
            $stmt->bind_param("i", $contentID);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()){
                $crewID = $row["crewID"];
                $sql2 = "SELECT name FROM CREW WHERE crewID = ?";
                $stmt2 = $dbConnection->prepare($sql2);
                if ($stmt2){
                    $stmt2->bind_param("i", $crewID);
                    $stmt2->execute();
                    $result2 = $stmt2->get_result();
                    $row2 = $result2->fetch_assoc();
                    $name = $row2["name"];
                    $sql3 = "SELECT role FROM CREW_ROLE WHERE crewID = ?";
                    $stmt3 = $dbConnection->prepare($sql3);
                    if ($stmt3){
                        $stmt3->bind_param("i", $crewID);
                        $stmt3->execute();
                        $result3 = $stmt3->get_result();
                        while ($row3 = $result3->fetch_assoc()){
                            $role = $row3["role"];
                            if ($role == "Actor"){
                                $actors[] = $name;
                            }
                            if ($role == "Director"){
                                $directors[] = $name;
                            }
                            if ($role == "Writer"){
                                $writers[] = $name;
                            }
                        }
                    }
                    else {
                        $response["status"] = "error";
                        $response["timestamp"] = time();
                        $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
                    }
                }
                else {
                    $response["status"] = "error";
                    $response["timestamp"] = time();
                    $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
                }
            }
            $data = [
                'actors' => $actors,
                'directors' => $directors,
                'writers' => $writers
            ];
            $response["status"] = "success";
            $response["timestamp"] = time();
            $response["data"] = $data;
        }
        else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function getAllCrewWork($dbConnection, $name){
        $sql = "SELECT crewID FROM CREW WHERE name = ?";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt){
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $crewID = $row["crewID"];
            $sql2 = "SELECT contentID FROM WORKS_ON WHERE crewID = ?";
            $stmt2 = $dbConnection->prepare($sql2);
            if ($stmt2){
                $stmt2->bind_param("i", $crewID);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                $data = [];
                while ($row2 = $result2->fetch_assoc()){
                    $contentID = $row2["contentID"];
                    $sql3 = "SELECT title, image, contentID FROM CONTENT WHERE contentID = ?";
                    $stmt3 = $dbConnection->prepare($sql3);
                    if ($stmt3){
                        $stmt3->bind_param("i", $contentID);
                        $stmt3->execute();
                        $result3 = $stmt3->get_result();
                        $row3 = $result3->fetch_assoc();
                        $title = $row3["title"];
                        $image = $row3["image"];
                        $contentID = $row3["contentID"];
                        $data[] = [
                            'title' => $title,
                            'image' => $image,
                            'contentID' => $contentID
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
        else {
            $response["status"] = "error";
            $response["timestamp"] = time();
            $response["data"] = array("message" => "HTTP Error Code 400: Failed to prepare statement");
        }

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
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
                $sql2 = "SELECT title FROM CONTENT WHERE contentID = ?";
                $stmt2 = $dbConnection->prepare($sql2);
                if ($stmt2){
                    $stmt2->bind_param("i", $contentID);
                    $stmt2->execute();
                    $result2 = $stmt2->get_result();
                    $row2 = $result2->fetch_assoc();
                    $title = $row2["title"];
                    $review[] = [
                        'title' => $title,
                        'comment' => $comment,
                        'rating' => $rating
                    ];
                    $data[] = $review;
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

        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function getContentReviews($dbConnection, $input){
        $contentID = $input["contentID"];
        $sql = "SELECT userID, comment, rating FROM REVIEW WHERE contentID = ?";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt){
            $stmt->bind_param("i", $contentID);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()){
                $userID = $row["userID"];
                $comment = $row["comment"];
                $rating = $row["rating"];
                $sql2 = "SELECT username FROM USERS WHERE userID = ?";
                $stmt2 = $dbConnection->prepare($sql2);
                if ($stmt2){
                    $stmt2->bind_param("i", $userID);
                    $stmt2->execute();
                    $result2 = $stmt2->get_result();
                    $row2 = $result2->fetch_assoc();
                    $username = $row2["username"];
                    $review = [
                        'username' => $username,
                        'comment' => $comment,
                        'rating' => $rating
                    ];
                    $data[] = $review;
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


        $jsonResponse = json_encode($response);
        header("Content-Type: application/json");
        echo $jsonResponse;
    }

    public function getTitle($dbConnection, $input){
        $contentID = $input["contentID"];
        $sql = "SELECT title FROM CONTENT WHERE contentID = ?";
        $stmt = $dbConnection->prepare($sql);
        $response = [];

        if ($stmt){
            $stmt->bind_param("i", $contentID);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $title = $row["title"];
            $response["status"] = "success";
            $response["timestamp"] = time();
            $response["data"] = array("title" => $title);
        }
        else {
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

if ($data["type"] == "getTitle"){
    $users = HoopAPI::instance();
    $users->getTitle($dbConnection, $data);
}

if ($data["type"] == "getContentReviews"){
    $listings = HoopAPI::instance();
    $listings->getContentReviews($dbConnection, $data);
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

if ($data["type"] == "getWatchList"){
    $watchlist = HoopAPI::instance();
    $watchlist->getWatchList($dbConnection, $data["userID"]);
}

if ($data["type"] == "getLiked"){
    $liked = HoopAPI::instance();
    $liked->getLiked($dbConnection, $data["userID"]);
}

if ($data["type"] == "recommended"){
    $rec = HoopAPI::instance();
    $rec->recommended($dbConnection, $data["userID"]);
}

if ($data["type"] == "addMovie"){
    $add = HoopAPI::instance();
    $add->addMovie($dbConnection, $data["title"], $data["language"], $data["genre"], $data["releaseDate"], $data["rating"], $data["productionStudio"], $data["image"], $data["summary"], $data["runtime"], $data["awards"], $data["age_rating"], $data["box_office"]);
}

if ($data["type"] == "addSeries"){
    $add = HoopAPI::instance();
    $add->addSeries($dbConnection, $data["title"], $data["language"], $data["genre"], $data["releaseDate"], $data["rating"], $data["productionStudio"], $data["image"], $data["summary"], $data["runtime"], $data["status"], $data["seasons"]);
}

if ($data["type"] == "sortTVByTitle"){
    $sort = HoopAPI::instance();
    $sort->sortTVByTitle($dbConnection);
}

if ($data["type"] == "sortMovieByTitle"){
    $sort = HoopAPI::instance();
    $sort->sortMovieByTitle($dbConnection);
}

if ($data["type"] == "sortTVByDate"){
    $sort = HoopAPI::instance();
    $sort->sortTVByDate($dbConnection);
}

if ($data["type"] == "sortMovieByDate"){
    $sort = HoopAPI::instance();
    $sort->sortMovieByDate($dbConnection);
}

if ($data["type"] == "sortTVByRating"){
    $sort = HoopAPI::instance();
    $sort->sortTVByRating($dbConnection);
}

if ($data["type"] == "sortMovieByRating"){
    $sort = HoopAPI::instance();
    $sort->sortMovieByRating($dbConnection);
}

if ($data["type"] == "filterTVByGenre"){
    $filter = HoopAPI::instance();
    $filter->filterTVByGenre($dbConnection, $data["genre"]);
}   

if ($data["type"] == "filterMovieByGenre"){
    $filter = HoopAPI::instance();
    $filter->filterMovieByGenre($dbConnection, $data["genre"]);
}

if ($data["type"] == "filterTVByYear"){
    $filter = HoopAPI::instance();
    $filter->filterTVByYear($dbConnection, $data["year"]);
}

if ($data["type"] == "filterMovieByYear"){
    $filter = HoopAPI::instance();
    $filter->filterMovieByYear($dbConnection, $data["year"]);
}

if ($data["type"] == "filterTVByLanguage"){
    $filter = HoopAPI::instance();
    $filter->filterTVByLanguage($dbConnection, $data["language"]);
}

if ($data["type"] == "filterMovieByLanguage"){
    $filter = HoopAPI::instance();
    $filter->filterMovieByLanguage($dbConnection, $data["language"]);
}

if ($data["type"] == "addToWatchList"){
    $add = HoopAPI::instance();
    $add->addToWatchList($dbConnection, $data["userID"], $data["contentID"]);
}

if ($data["type"] == "addToLiked"){
    $add = HoopAPI::instance();
    $add->addToLiked($dbConnection, $data["userID"], $data["contentID"]);
}

if ($data["type"] == "removeFromWatchList"){
    $remove = HoopAPI::instance();
    $remove->removeFromWatchList($dbConnection, $data["userID"], $data["contentID"]);
}

if ($data["type"] == "moreContentInfo"){
    $more = HoopAPI::instance();
    $more->moreContentInfo($dbConnection, $data["contentID"]);
}

if ($data["type"] == "getCrew"){
    $crew = HoopAPI::instance();
    $crew->getCrew($dbConnection, $data["contentID"]);
}

if ($data["type"] == "getAllCrewWork"){
    $crew = HoopAPI::instance();
    $crew->getAllCrewWork($dbConnection, $data["name"]);
}

if ($data["type"] == "getUserReviews"){
    $reviews = HoopAPI::instance();
    $reviews->getUserReviews($dbConnection, $data);
}

$dbConnection->close();
?>