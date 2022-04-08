<?php include 'header.php' ?>
<?php include 'template.php' ?>
<?php include 'data.php' ?>



<div class="box">
    <div id="header" class="clearfix">
        <div id="toggleMonth">
            <button class="today">today</button>
            <div class="changeMonth">
                <i class="icon-angle-left"></i>
                <!-- <span><?=date('Y') ?>/<?=date('m') ?>/<?=date('d') ?></span> -->
                <i class="icon-angle-right"></i>
            </div>

        </div>
        <div id="date" data-year="<?=date('Y') ?>" data-month="<?=date('n') ?>" data-day="<?=date('d') ?>">
            <div>
                <span><?=date('Y') ?>年</span> /
                <span class="thisMonth"><?php echo date('n') ?>月</span>
            </div>
            <div class="edit"> <button>˙˙˙</button> </div>
        </div>
    </div>

    <div id="calendar">
        <div id="weeks">
            <div class="week red">sun</div>
            <?php foreach ($weeks as $key => $week): ?>
                <div class="week"> <?=$week ?> </div>
            <?php endforeach ?>
            <div class="week red">sat</div>
        </div>
        <div id="dates">
            <?php foreach ($dates as $key => $day): ?>
                <div class="day-block <?php echo (is_null($day)) ? 'empty' : '' ?>" data-day="<?=$day ?>">
                    <!-- 利用 php三元運算子：來判斷是否添加 'empty'class。 -->
                    <div class="day"> <?=$day ?> </div>
                    <div class="events">
                        <!-- use Handlebars (template.php) -->
                        <!-- <div class="event">
                            <div class="title">title</div>
                            <div class="from">10:11</div>
                        </div> -->
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>

    <!-- panel -->
    <div id="todoMode" >
        <div class="todoEdit">
            <input type="text" class="title" placeholder="新增標題與時間" name="title">
            <button class="close">x</button>
        </div>

        <div class="error-msg">
            <div class="alert alert-danger">
            </div>
        </div>

        <div class="time">
            <div class="theDay">
                <span class="month"></span> / <span class="day"></span>
                <input type="hidden" name="month">
                <input type="hidden" name="day">
            </div>
            <label for="from">from</label>
            <input type="time" id="from" name="start_time">
            <label for="to">to</label>
            <input type="time" id="to" name="end_time">
        </div>

        <div class="description">
            <label>description</label>
            <textarea id="description" placeholder="新增備註" name="description"></textarea>
        </div>

        <div class="buttons">
            <button class="create">create</button>
            <button class="update">update</button>
            <button class="cancel">cancel</button>
            <button class="delete">delete</button>

        </div>
    </div>
</div>

<?php include 'footer.php' ?>

