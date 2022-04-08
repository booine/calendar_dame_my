<?php
Header('Content-Type:application/json;charset=utf-8');
include '../../db.php';
include '../HttpStatusCode.php';

// PDO連線 MySQL
try {
    // PDO連線 MySQL 資料庫並建立物件。
    $pdo = new PDO("mysql:host = $db[host];dbname = $db[dbname];port = $db[port];charset = $db[charset]", $db['username'], $db['password']);
    // echo "資料庫連線成功\n";
    // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}
catch (PDOException $e)
{
    echo 'Database connection failed!連線失敗！';
    // echo 'Error: ' . $e->getMessage();
    // exit ->將程式停下不再執行後面程式碼。
    exit;
};

// Validation and Update data to DB
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Validation title can't be blank and time range
    // title
    if (empty($_POST['title']))
    {
        new HttpStatusCode(400, '標題不可為空！');
        // http_response_code(400);
        // echo '標題不可為空！';
        // exit;
    }
    // time range
    $startTime = explode(':', $_POST['start_time']); // '10:11' -> ["10","11"]
    $endTime = explode(':', $_POST['end_time']); // '10:11' -> ["10","11"]
    if ($startTime[0] > $endTime[0] || ($startTime[0] == $endTime[0] && $startTime[1] > $endTime[1]))
    {
        new HttpStatusCode(400, '時間有誤！請重新選擇。');
        // http_response_code(400);
        // echo '時間有誤！請重新選擇。';
        // exit;
    }

    // Update data to DB
    $sql = "UPDATE $db[dbname].events SET title=:title,`year`=:year,`month`=:month,`day`=:day,start_time=:start_time,end_time=:end_time,`description`=:description WHERE id=:id ";
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
    $statement->bindValue(':title', $_POST['title'], PDO::PARAM_STR);
    $statement->bindValue(':year', $_POST['year'], PDO::PARAM_INT);
    $statement->bindValue(':month', $_POST['month'], PDO::PARAM_INT);
    $statement->bindValue(':day', $_POST['day'], PDO::PARAM_INT);
    $statement->bindValue(':start_time', $_POST['start_time'], PDO::PARAM_STR);
    $statement->bindValue(':end_time', $_POST['end_time'], PDO::PARAM_STR);
    $statement->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
    $result = $statement->execute();

    if ($result)
    {
        // select the id's data from DB to the front
        $sql = "SELECT * FROM $db[dbname].events WHERE id=:id";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $result = $statement->execute();
        $event = $statement->fetch(PDO::FETCH_ASSOC);

        // modify TIME string 10:11:00 -> 10:11
        $event['start_time'] = substr($event['start_time'], 0, 5);
        $event['end_time'] = substr($event['end_time'], 0, 5);

        echo json_encode($event);
    }
    else
    {
        new HttpStatusCode(400, 'Update failed.');
    }

}
catch (PDOException $e)
{
    echo 'Error: ' . $e->getMessage();
};
?>

