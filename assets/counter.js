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
        box.$button = box.$this.find('.start-stop');
        box.$time = box.$this.find('.time-field');

        box.$this.on('click', '.minimize', box.minimize);
        box.$this.on('click', '.start-stop', box.count);
        box.$this.on('click', '.delete-time-entry', box.delete_time);
        box.$this.on('click', '.time-entry-time', box.delete_time);
        box.$this.filter('.minimized')
            .draggable({handle: ".handle"})
            .css('transition', 'none');

        box.time_started = getCookie('time_started');
        if (box.time_started != undefined) {
            box.tick_tack();
            box.$button.addClass('active');
        }
    };

    box.minimize = function (e) {
        e.preventDefault();
        if (box.$this.hasClass('minimized')) {
            box.$this.draggable("destroy");

            box.$this
                .attr('style', '');
            box.$this.removeClass('minimized');
            setCookie('time_modal_minimized', 0, {expires: 3600});

        } else {
            box.$this.addClass('minimized');
            setTimeout(function () {
                box.$this
                    .draggable({handle: ".handle"})
                    .css('transition', 'none');
            }, 1000);

            setCookie('time_modal_minimized', 1, {expires: 3600})

        }

    };

    box.count = function (e) {
        e.preventDefault();
        var $this = $(this);

        if ($this.hasClass('active')) {
            clearInterval(box.countInterval);
            $this.removeClass('active');
            $this.text($this.data('start'));

            box.register_time();


        } else {

            $this.text($this.data('stop'));

            if (box.$time.val() == '') box.time_started = Date.now() / 1000;
            else box.time_started = Date.now() / 1000 - working_in_sec(box.$time.val());

            setCookie('time_started', box.time_started);

            //
            box.tick_tack();
            $this.addClass('active');
        }

    };

    box.tick_tack = function () {
        box.countInterval = setInterval(function () {
            box.$time.val(working_to_string(Date.now() / 1000 - box.time_started));
        }, 1000);
    };

    box.register_time = function () {


        $.post(time_tracking.ajax_url, {
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

            box.$time.data('start', '');
            setCookie('time_started', '', {
                expires: -1
            })
        });

    };
    box.isert_new_time = function (comment_ID) {
        $('<tr>' +
            '<td class="time-item-id">' + comment_ID + '</td>' +
            '<td class="time-item-message">' + box.$this.find('.description-field').val() +
            ' <a href="#" class="delete-time-entry">Remove</a>' + '</td>' +
            '<td class="time-entry-time">' + box.$this.find('.time-field').val() + '</td>' +
            '</tr>').prependTo(box.$this.find('.time-entries-table').find('tbody'));

        box.$this.find('.description-field').val('');
        box.$this.find('.time-field').val('');
    };

    box.delete_time = function (e) {
        e.preventDefault();

        var $this = $(this);

        $.post(time_tracking.ajax_url, {
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

    function setCookie(name, value, options) {
        options = options || {};
        var expires = options.expires;

        if (typeof expires == "number" && expires) {
            var d = new Date();
            d.setTime(d.getTime() + expires * 1000);
            expires = options.expires = d;
        }
        if (expires && expires.toUTCString) {
            options.expires = expires.toUTCString();
        }

        value = encodeURIComponent(value);

        var updatedCookie = name + "=" + value;

        for (var propName in options) {
            updatedCookie += "; " + propName;
            var propValue = options[propName];
            if (propValue !== true) {
                updatedCookie += "=" + propValue;
            }
        }

        document.cookie = updatedCookie;
    }

    function getCookie(name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }


})(jQuery);

