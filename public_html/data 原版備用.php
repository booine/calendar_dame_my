<?php
include '../db.php';

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

// 前端傳入year / month
if ($_POST['year'] && $_POST['month'])
{
    $year = $_POST['year'];
    // echo "this year: " . $year;
    $month = $_POST['month'];
    // echo "this month: " . $month;

}
else
{
    $year = date('Y');
    $month = date('n'); // 'n' 數字表示的月份，沒有前導零
}

// $year = date('Y');
// $month = date('n');

// select all event data from DB to web
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM $db[dbname].events WHERE `year`=:year  AND `month`=:month ORDER BY `day`,start_time ASC";
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':year', $year, PDO::PARAM_INT);
    $statement->bindValue(':month', $month, PDO::PARAM_INT);
    $statement->execute();
    $events = $statement->fetchAll(PDO::FETCH_ASSOC);
    /* $events = [['id'=>'1','title'=>'...','start_time'=>'10:00:00',...],['id'=>'1','title'=>'...','start_time'=>'10:00:00',...],[...],[...]] */

    if ($events)
    {
        foreach ($events as $key => $event)
        {
            // $event = ['id'=>'1','title'=>'...','start_time'=>'10:00:00',...];
            // 要重新寫回 $events 就要用 $events[$key]['start_time']，而不是 $event['end_time']。
            $events[$key]['start_time'] = substr($event['start_time'], 0, 5);
            $events[$key]['end_time'] = substr($event['end_time'], 0, 5);

        }
        // echo json_encode($events, JSON_NUMERIC_CHECK);
    }
}
catch (PDOException $e)
{
    echo 'Error: ' . $e->getMessage();
};
// $month = $_POST['month']; 我卡在這...為何 $month 會一直是 4...似乎是因為檔案加載的順序有關...
// $month = $_POST['month']; //用傳入的 month 就會出錯...

date_default_timezone_set("Asia/Taipei"); //PHP 時區 timezone 設定，一開始是未設置所以會以標準時區 ：也就是GMT+0 (Taiwan is GMT-8)
$weeks = ['mon', 'tue', 'wed', 'thu', 'fri'];
// 月曆上的日期：
// 這個月有幾天？28/29/30/31 (cal_days_in_month — 返回某個曆法中某年中某月的天數)
$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
// 1號是星期幾？(用來推論 [前面] 要塞幾個 empty)
$firstDayOfMonth = new DateTime("$year-$month-1");
$frontEmpty = $firstDayOfMonth->format('w'); //推論 [前面] 要塞幾個 empty
// 最後一天是星期幾？(用來推論 [後面] 要塞幾個 empty)
$lastDayOfMonth = new DateTime("$year-$month-$days");
$backEmpty = 6 - $lastDayOfMonth->format('w'); //推論 [後面] 要塞幾個 empty

$dates = [];
for ($i = 1; $i <= $frontEmpty; $i++)
{
    $dates[] = null;
}
for ($i = 1; $i <= $days; $i++)
{
    $dates[] = $i;
}
for ($i = 1; $i <= $backEmpty; $i++)
{
    $dates[] = null;
}

?>
<script>
    var events = <?php echo json_encode($events, JSON_NUMERIC_CHECK) ?>;
</script>

