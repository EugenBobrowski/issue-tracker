/**
 * Created by eugen on 21.10.16.
 */

'use strict';

(function ($) {
    console.log('asgfdsfasdfasf');

    var $time_modal;

    $(document).ready(function () {
        box.ready();
        console.log(time_tracking);


    });

    var box = {};

    box.ready = function () {
        box.$this = $('.time-tacking-modal');
        box.$this.on('click', '.minimize', box.minimize);
        box.$this.on('click', '.start-stop', box.count);
        box.$this.on('click', '.delete-time-entry', box.delete_time);

    };

    box.minimize = function (e) {
        e.preventDefault();
        console.log(box.$this.hasClass('minimize'));
        if (box.$this.hasClass('minimized')) {
            box.$this.draggable("destroy");

            box.$this
                .attr('style', '');
            box.$this.removeClass('minimized');
        } else {

            box.$this.addClass('minimized');
            setTimeout(function () {
                box.$this
                    .draggable({handle: ".handle"})
                    .css('transition', 'none');
            }, 1000);

        }

    };

    box.count = function (e) {
        e.preventDefault();
        var $this = $(this);
        var $field = box.$this.find('.time-field');

        if ($this.hasClass('active')) {
            clearInterval(box.countInterval);
            $this.removeClass('active');
            $this.text($this.data('start'));

            box.register_time();

        } else {
            $this.text($this.data('stop'));

            if ($field.val() == '') {
                $field.data('start', Date.now() / 1000);
            }
            else {
                $field.data('start', Date.now() / 1000 - working_in_sec($field.val()));
            }
            //
            box.countInterval = setInterval(function () {
                $field.val(working_to_string(Date.now() / 1000 - $field.data('start')));
            }, 1000);
            $this.addClass('active');
        }

    };

    box.register_time = function () {


        var x = $.post(time_tracking.ajax_url, {
            action: 'track_time',
            _wpnonce: time_tracking.nonce,
            time: working_in_sec(box.$this.find('.time-field').val()),
            desc: box.$this.find('.description-field').val(),
            issue: box.$this.find('.description-field').val()
        }, function (response) {
            var comment_ID = parseInt(response);
            if (!comment_ID) {
                alert(response);
                return;
            }
            box.isert_new_time(comment_ID);

            return 'sdfgf';
        });

    };
    box.isert_new_time = function (comment_ID) {
        $('<tr>' +
            '<td class="time-item-id">' + comment_ID + '</td>' +
            '<td class="time-item-message">' + box.$this.find('.description-field').val() + '</td>' +
            '<td class="time-item-time">' + box.$this.find('.time-field').val() + '</td>' +
            '</tr>').prependTo(box.$this.find('.time-entries-table').find('tbody'));

        box.$this.find('.description-field').val('');
        box.$this.find('.time-field').val('');
    };

    box.delete_time = function (e) {
        e.preventDefault();

        var $this = $(this);

        var x = $.post(time_tracking.ajax_url, {
            action: 'delete_time',
            _wpnonce: time_tracking.nonce,
            entry_ID: $this.parents('tr').find('.time-item-id').text()
        }, function (response) {
            $this.parents('tr').remove();
            var comment_ID = parseInt(response);
            if (!comment_ID) {
                alert(response);
                return;
            }
            return 'sdfgf';
        });

    };


    function working_in_sec(str) {
        //1d2.5h5m6s = 1 * 8 * 3600 + 2.5 * 3600 + 5 * 60 + 6
        var time, sec = 0;

        //days
        if (str.indexOf('d') != -1) {
            time = str.split('d', 2);
            if (!isNaN(parseFloat(time[0])))
            sec += parseFloat(time[0]) * 8 * 3600;
            if (time.length == 2) str = time[1];
            else str = time[0];
        }

        //hour
        if (str.indexOf('h') != -1 || ( str.indexOf('m') == -1 && str.indexOf('s') == -1 )) {
            time = str.split('h', 2);
            if (!isNaN(parseFloat(time[0])))
            sec += parseFloat(time[0]) * 3600;
            if (time.length == 2) str = time[1];
            else str = time[0];
        }

        //mins
        if (str.indexOf('m') != -1) {
            time = str.split('m', 2);
            if (!isNaN(parseFloat(time[0])))
            sec += parseFloat(time[0]) * 60;
            if (time.length == 2) str = time[1];
            else str = time[0];
        }

        //sec
        if (str.indexOf('s') != -1) {
            time = str.split('s', 2);
            if (!isNaN(parseFloat(time[0])))
            sec += parseInt(time[0]);
            if (time.length == 2) str = time[1];
            else str = time[0];
        }

        return sec;

    }

    function working_to_string(sec) {
        var string = '';

        // calculate (and subtract) whole days
        var days = Math.floor(sec / 28800);
        sec -= days * 28800;
        if (days > 0) string += days + 'd';

        // calculate (and subtract) whole hours
        var hours = Math.floor(sec / 3600) % 24;
        sec -= hours * 3600;
        if (hours > 0 || days > 0) string += hours + 'h';

        // calculate (and subtract) whole minutes
        var minutes = Math.floor(sec / 60) % 60;
        sec -= minutes * 60;
        if (minutes > 0 || hours > 0 || days > 0) string += minutes + 'm';

        // what's left is seconds
        var seconds = parseInt(sec) % 60;
        string += seconds + 's';

        return string;

    }


})(jQuery);

