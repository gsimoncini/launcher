/*!
 * jQuery Birthday Picker: v1.4 - 10/16/2011
 * http://abecoffman.com/stuff/birthdaypicker
 *
 * Copyright (c) 2010 Abe Coffman
 * Dual licensed under the MIT and GPL licenses.
 *
 */

(function($) {

    // plugin variables
    var months = {
        "short": ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
        "long": ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"]},
    todayDate = new Date(),
            todayYear = todayDate.getFullYear(),
            todayMonth = todayDate.getMonth() + 1,
            todayDay = todayDate.getDate();


    $.fn.birthdaypicker = function(options) {

        var settings = {
            "maxAge": 100,
            "minAge": 0,
            "futureDates": false,
            "maxYear": todayYear,
            "dateFormat": "littleEndian",
            "monthFormat": "short",
            "placeholder": true,
            "legend": "",
            "defaultDate": false,
            "fieldName": "birthdate",
            "fieldId": "birthdate",
            "hiddenDate": true,
            "onChange": null,
            "tabindex": null
        };

        return this.each(function() {

            if (options) {
                $.extend(settings, options);
            }

            // Create the html picker skeleton
            var $fieldset = $("<fieldset class='birthday-picker'></fieldset>"),
                    $row = $("<div class='row'></div>"),
                    $yearColumn = $("<div class='col-xs-3'></div>"),
                    $monthColumn = $("<div class='col-xs-6'></div>"),
                    $dayColumn = $("<div class='col-xs-3'></div>"),
                    $year = $("<select class='birth-year form-control input-sm' name='birth[year]'></select>"),
                    $month = $("<select class='birth-month form-control input-sm' name='birth[month]'></select>"),
                    $day = $("<select class='birth-day form-control input-sm' name='birth[day]'></select>");

            if (settings["legend"]) {
                $("<legend>" + settings["legend"] + "</legend>").appendTo($fieldset);
            }

            var tabindex = settings["tabindex"];

            $yearColumn.append($year);
            $monthColumn.append($month);
            $dayColumn.append($day);

            // Deal with the various Date Formats
            if (settings["dateFormat"] == "bigEndian") {
                $row.append($yearColumn).append($monthColumn).append($dayColumn);
                if (tabindex != null) {
                    $year.attr('tabindex', tabindex);
                    $month.attr('tabindex', tabindex++);
                    $day.attr('tabindex', tabindex++);
                }
            } else if (settings["dateFormat"] == "littleEndian") {
                $row.append($dayColumn).append($monthColumn).append($yearColumn);
                if (tabindex != null) {
                    $day.attr('tabindex', tabindex);
                    $month.attr('tabindex', tabindex++);
                    $year.attr('tabindex', tabindex++);
                }
            } else {
                $row.append($monthColumn).append($dayColumn).append($yearColumn);
                if (tabindex != null) {
                    $month.attr('tabindex', tabindex);
                    $day.attr('tabindex', tabindex++);
                    $year.attr('tabindex', tabindex++);
                }
            }

            $fieldset.append($row);

            // Add the option placeholders if specified
            if (settings["placeholder"]) {
                $("<option value='0'>- A&ntilde;o -</option>").appendTo($year);
                $("<option value='0'>- Mes -</option>").appendTo($month);
                $("<option value='0'>- D&iacute;a -</option>").appendTo($day);
            }

            var hiddenDate;
            if (settings["defaultDate"]) {
                var arrayDate = settings["defaultDate"].split('-'),
                        defDate = new Date(arrayDate[0], arrayDate[1] - 1, arrayDate[2]),
                        defYear = defDate.getFullYear(),
                        defMonth = defDate.getMonth() + 1,
                        defDay = defDate.getDate();
                if (defMonth < 10)
                    defMonth = "0" + defMonth;
                if (defDay < 10)
                    defDay = "0" + defDay;
                hiddenDate = defYear + "-" + defMonth + "-" + defDay;
            }

            // Create the hidden date markup
            if (settings["hiddenDate"]) {
                $("<input type='hidden' name='" + settings["fieldName"] + "'/>")
                        .attr("id", settings["fieldId"])
                        .val(hiddenDate)
                        .appendTo($fieldset);
            }

            // Build the initial option sets
            var startYear = todayYear - settings["minAge"];
            var endYear = todayYear - settings["maxAge"];
            if (settings["futureDates"] && settings["maxYear"] != todayYear) {
                if (settings["maxYear"] > 1000) {
                    startYear = settings["maxYear"];
                }
                else {
                    startYear = todayYear + settings["maxYear"];
                }
            }
            for (var i = startYear; i >= endYear; i--) {
                $("<option></option>").attr("value", i).text(i).appendTo($year);
            }
            for (var j = 0; j < 12; j++) {
                $("<option></option>").attr("value", j + 1).text(months[settings["monthFormat"]][j]).appendTo($month);
            }
            for (var k = 1; k < 32; k++) {
                $("<option></option>").attr("value", k).text(k).appendTo($day);
            }
            $(this).append($fieldset);

            // Set the default date if given
            if (settings["defaultDate"]) {
                var arrayDate = settings["defaultDate"].split('-'),
                        date = new Date(arrayDate[0], arrayDate[1] - 1, arrayDate[2]);
                $year.val(date.getFullYear());
                $month.val(date.getMonth() + 1);
                $day.val(date.getDate());
            }

            // Update the option sets according to options and user selections
            $fieldset.change(function() {
                // todays date values
                var todayDate = new Date(),
                        todayYear = todayDate.getFullYear(),
                        todayMonth = todayDate.getMonth() + 1,
                        todayDay = todayDate.getDate(),
                        // currently selected values
                        selectedYear = parseInt($year.val(), 10),
                        selectedMonth = parseInt($month.val(), 10),
                        selectedDay = parseInt($day.val(), 10),
                        // number of days in currently selected year/month
                        actMaxDay = (new Date(selectedYear, selectedMonth, 0)).getDate(),
                        // max values currently in the markup
                        curMaxMonth = parseInt($month.children(":last").val()),
                        curMaxDay = parseInt($day.children(":last").val());

                // Dealing with the number of days in a month
                // http://bugs.jquery.com/ticket/3041
                if (curMaxDay > actMaxDay) {
                    while (curMaxDay > actMaxDay) {
                        $day.children(":last").remove();
                        curMaxDay--;
                    }
                } else if (curMaxDay < actMaxDay) {
                    while (curMaxDay < actMaxDay) {
                        curMaxDay++;
                        $day.append("<option value=" + curMaxDay + ">" + curMaxDay + "</option>");
                    }
                }

                // Dealing with future months/days in current year
                // or months/days that fall after the minimum age
                if (!settings["futureDates"] && selectedYear == startYear) {
                    if (curMaxMonth > todayMonth) {
                        while (curMaxMonth > todayMonth) {
                            $month.children(":last").remove();
                            curMaxMonth--;
                        }
                        // reset the day selection
                        $day.children(":first").attr("selected", "selected");
                    }
                    if (selectedMonth === todayMonth) {
                        while (curMaxDay > todayDay) {
                            $day.children(":last").remove();
                            curMaxDay -= 1;
                        }
                    }
                }

                // Adding months back that may have been removed
                // http://bugs.jquery.com/ticket/3041
                if (selectedYear != startYear && curMaxMonth != 12) {
                    while (curMaxMonth < 12) {
                        $month.append("<option value=" + (curMaxMonth + 1) + ">" + months[settings["monthFormat"]][curMaxMonth] + "</option>");
                        curMaxMonth++;
                    }
                }

                // update the hidden date
                if ((selectedYear * selectedMonth * selectedDay) != 0) {
                    if (selectedMonth < 10)
                        selectedMonth = "0" + selectedMonth;
                    if (selectedDay < 10)
                        selectedDay = "0" + selectedDay;
                    hiddenDate = selectedYear + "-" + selectedMonth + "-" + selectedDay;
                    $(this).find('#' + settings["fieldId"]).val(hiddenDate);
                    if (settings["onChange"] != null) {
                        settings["onChange"](hiddenDate);
                    }
                }
            });
        });
    };
})(jQuery);
