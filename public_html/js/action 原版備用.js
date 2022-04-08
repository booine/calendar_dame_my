(function () {
    var today = new Date();
    let year = today.getFullYear();
    let month = today.getMonth() + 1; // 獲取月資訊（從0開始 範圍：0-11）
    let day = today.getDate();

    $('document').ready(function () {

        $('.today').click(function (e) {
            console.log(year + "年", month + "月", day + "日");
            $('#dates').find('.day-block[data-day="' + day + '"]').addClass('focus');
            $('#date').find('.thisMonth').text(month + '月');
        })
        $('.icon-angle-left').click(function (e) {
            var year = $('#date').data('year');
            var month = parseInt($('#date').find('.thisMonth').text()) - 1;

            if (month >= 1) {
                $('#date').find('.thisMonth').text(month + '月');
                console.log(year, month);
                $.post("data.php", {
                        year: year,
                        month: month
                    })
                    .done(function (data, textStatus, xhr) {
                        // 先改變月曆畫面，再重新塞event
                        console.log(xhr);
                        console.log(textStatus);
                    })
                    .fail(function (xhr) {
                        console.log(xhr.responseText);
                    })
                "json";
            }


        })
        $('.icon-angle-right').click(function (e) {
            var year = $('#date').data('year');
            var month = parseInt($('#date').find('.thisMonth').text()) + 1;
            if (month <= 12) {
                $('#date').find('.thisMonth').text(month + '月');
                console.log(year, month);
                $.post("data.php", {
                        year: year,
                        month: month
                    })
                    .done(function (data, textStatus, xhr) {
                        console.log(xhr);
                        console.log(textStatus);
                    })
                    .fail(function (xhr) {
                        console.log(xhr.responseText);
                    })
                "json";
            }


        })

        var source = $('#event-template').html();
        var eventTemplate = Handlebars.compile(source);

        // load DB event data to web
        /* events = [{id:1,title:'...',...},{id:2,title:'...',...}]; */
        // console.log('events: ', events);
        $.each(events, function (index, item) {
            //  item = {id:1,title:'...',...};
            var eventUI = eventTemplate(item);
            $('#calendar').find('.day-block[data-day="' + item.day + '"]').find('.events').append(eventUI);
            // Position today:
            var today = parseInt($('#date').data('day')); //parseInt();字串轉數值。
            // $('#dates').find('.day-block[data-day="' + today + '"]').addClass('focus');
            $('#date').find('.thisMonth').text(item.month + '月');

        });
        var panel = {
            el: '#todoMode',
            selectedDayBlock: null,
            selectedEvent: null,
            open: function (isNew, e) {

                panel.clear();
                panel.updateDate(e);
                panel.hideError();

                $(panel.el).addClass('open').css({
                    top: e.pageY + 'px',
                    left: e.pageX + 'px'
                }).find('.title[name=title]').focus();


                if (isNew) {
                    $(panel.el).addClass('new').removeClass('old');

                } else {
                    $(panel.el).addClass('old').removeClass('new');

                }

            },
            close: function () {
                $(panel.el).removeClass('open');

            },
            updateDate: function (e) {

                var month = $('#date').data('month');
                if ($(e.currentTarget).is('.day-block')) {
                    var day = $(e.currentTarget).data('day');
                } else {
                    var day = $(e.currentTarget).closest('.day-block').data('day');
                }


                // console.log(month, day);
                $(panel.el).find('.month').text(month);
                $(panel.el).find('.day').text(day);

                $(panel.el).find('[name=month]').val(month);
                $(panel.el).find('[name=day]').val(day);
            },
            clear: function () {
                $(panel.el).find('[name=title]').val('');
                $(panel.el).find('[name=start_time]').val('');
                $(panel.el).find('[name=end_time]').val('');
                $(panel.el).find('[name=description]').val('');
            },
            showError: function (msg) {
                $(panel.el).find('.error-msg').addClass('open').find('.alert').text(msg);
            },
            hideError: function () {
                $(panel.el).find('.error-msg').removeClass('open');
            },


        }

        $('.day-block').dblclick(function (e) {
            // $(e.currentTarget) -> .day-block
            // selectedDayBlock -> 用以紀錄被點擊的那個 .day-block
            if (!($(e.currentTarget).is('.empty'))) {
                panel.selectedDayBlock = $(e.currentTarget);
                panel.open(true, e);
            }

        }).on('click', '.event', function (e) {
            // $(e.currentTarget) -> .event
            // selectedEvent -> 用以紀錄被點擊的 .event
            panel.selectedEvent = $(e.currentTarget);
            // selectedDayBlock -> 用以紀錄被點擊的 .event 位於哪個.day - block
            panel.selectedDayBlock = $(e.currentTarget).closest('.day-block');

            e.stopPropagation(); //阻止「事件冒泡」。
            panel.open(false, e);

            /* 讀取 event date */
            // 1.get clicked .event [data-id].
            var id = panel.selectedEvent.data('id');
            // console.log(id);
            // 2.AJAX call - use id to get event data from DB.
            $.post("event/read.php", {
                    id: id
                })
                .done(function (data, textStatus, xhr) {
                    // console.log('後端回傳：', data);
                    // 3.insert event data(return from DB) into html.
                    $(panel.el).find('[name=title]').val(data.title);
                    $(panel.el).find('[name=start_time]').val(data.start_time);
                    $(panel.el).find('[name=end_time]').val(data.end_time);
                    $(panel.el).find('[name=description]').val(data.description);
                })
                .fail(function (xhr) {
                    // console.log(xhr.responseText);
                    panel.showError(xhr.responseText);
                })
            "json";


        })




        /* buttons:create/update/cancel/delete/cancel */
        $(panel.el).on('click', 'button', function (e) {
            if ($(this).is('.create') || $(this).is('.update')) {
                if ($(this).is('.create')) {
                    var action = "event/create.php";
                }
                if ($(this).is('.update')) {
                    var action = "event/update.php";
                    //remove old event data
                    panel.selectedEvent.remove();

                }
                // 1.collect panel data
                var eventData = {
                    id: $(panel.selectedEvent).data('id'),
                    title: $(panel.el).find('[name=title]').val(),
                    year: $('#date').data('year'),
                    month: $(panel.el).find('[name=month]').val(),
                    day: $(panel.el).find('[name=day]').val(),
                    start_time: $(panel.el).find('[name=start_time]').val(),
                    end_time: $(panel.el).find('[name=end_time]').val(),
                    description: $(panel.el).find('[name=description]').val(),
                };

                /* AJAX call - insert DB */
                $.post(action, eventData)
                    .done(function (data, textStatus, xhr) {
                        /* insert the data to front web */
                        var eventUI = eventTemplate(data);

                        // 依照時間排序：
                        /* 新插入的時間如果比既有的.event時間來的「早」就在最前面插入 */
                        panel.selectedDayBlock.find('.event').each(function (index, item) {
                            // item = this = DOM物件
                            // each 時就會第一個讀到「最早的.event 時間」，所以第一個比完如果新插入的事件更早的話就直接在該.event前面插入，後面也可以不用比了。
                            var eventStartTime = $(item).find('.from').text().split(':');
                            var newEventStartTime = data.start_time.split(':');
                            // console.log(eventStartTime, newEventStartTime);
                            if (eventStartTime[0] > newEventStartTime[0] || (eventStartTime[0] == newEventStartTime[0] && eventStartTime[1] > newEventStartTime[1])) {
                                $(item).before(eventUI);
                                return false; //中斷比較
                            }

                        })
                        // 前面 each比較後新插入的事件是最晚(還沒插入)的話，就在.events最後面插入。
                        // (...).length == 0 代表沒有 。 
                        if (panel.selectedDayBlock.find('[data-id="' + data.id + '"]').length == 0) {
                            // 把填入輸入值的 event template 塞回 .events中
                            $(panel.selectedDayBlock).find('.events').append(eventUI);

                            // panel.selectedDayBlock.removeClass('selected');

                        }
                        panel.close();

                    })
                    .fail(function (xhr) {
                        // console.log(xhr.responseText);
                        panel.showError(xhr.responseText);
                    })
                "json";
            }
            if ($(this).is('.cancel')) {
                // console.log('cancel');
                panel.close();
            }
            if ($(this).is('.delete')) {
                var id = panel.selectedEvent.data('id');
                $.post("event/delete.php", {
                        id: id
                    })
                    .done(function (data, textStatus, xhr) {
                        var title = panel.selectedEvent.find('.title').text();
                        var result = confirm(`Are you sure you want to delete " ${title} " ? \nThis action cannot be undone.`);
                        if (result) {
                            panel.selectedEvent.remove();
                            panel.close();
                        }

                    })
                    .fail(function (xhr) {
                        panel.showError(xhr.responseText);
                    })
                "json";

            }
            if ($(this).is('.close')) {
                panel.close();
            }
        })

        /* keyboards: cancel/close panel */
        $(panel.el).keydown(function (e) {
            /* 注意！panel上一定要有「focus」 */
            // The 【 event.which 】 property returns which keyboard key or mouse button was pressed for the event.
            // console.log(e.which);
            if (e.which === 27) {
                panel.close();
            }

        });


    });
})();