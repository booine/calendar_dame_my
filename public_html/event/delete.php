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

// Delete event data from DB.
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Delete event data with id from DB
    $sql = "DELETE FROM $db[dbname].events WHERE id=:id ";
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
    $result = $statement->execute();

    if ($result)
    {
        echo json_encode('刪除成功', JSON_UNESCAPED_UNICODE);
    }

}
catch (PDOException $e)
{
    echo 'Error: ' . $e->getMessage();
};
?>

