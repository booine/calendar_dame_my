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

// Select event data from DB and return to front.
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Select event data from DB
    $sql = "SELECT * FROM $db[dbname].events WHERE id=:id ";
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
    $result = $statement->execute();
    $event = $statement->fetch(PDO::FETCH_ASSOC);
    //$event = ["id"=>"n","title"=>"...","start_time"=>"10:00:00",....];

    if ($event)
    {

        // modify TIME string 10:11:00 -> 10:11
        $event['start_time'] = substr($event['start_time'], 0, 5);
        $event['end_time'] = substr($event['end_time'], 0, 5);

        echo json_encode($event);
    }

}
catch (PDOException $e)
{
    echo 'Error: ' . $e->getMessage();
};
?>

