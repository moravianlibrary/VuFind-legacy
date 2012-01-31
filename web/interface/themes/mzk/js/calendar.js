putHoldInit = function() {
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event,
        cal1,
        over_cal = false,
        cur_field = '';

    var initCalendar = function() {
        cal1 = new YAHOO.widget.Calendar("cal1","cal1Container", {START_WEEKDAY: 1});
        cal1.cfg.setProperty("DATE_FIELD_DELIMITER", ".");
        cal1.cfg.setProperty("MDY_DAY_POSITION", 1);
        cal1.cfg.setProperty("MDY_MONTH_POSITION", 2);
        cal1.cfg.setProperty("MDY_YEAR_POSITION", 3);
        cal1.cfg.setProperty("MD_DAY_POSITION", 1);
        cal1.cfg.setProperty("MD_MONTH_POSITION", 2);
        cal1.cfg.setProperty("MONTHS_LONG", ["Leden", "Únor", "Březen", "Duben", "Květen", "Červen",
           "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"]); 
        cal1.cfg.setProperty("WEEKDAYS_SHORT", ["Ne", "Po", "Út", "St", "Čt", "Pá", "So"]);
        cal1.selectEvent.subscribe(getDate, cal1, true);
        cal1.renderEvent.subscribe(setupListeners, cal1, true);
        Event.addListener(['calendar'], 'focus', showCal);
        Event.addListener(['calendar'], 'blur', hideCal);
        cal1.render();
        // cal1.cfg.setProperty('pagedate', new Date(date), true);
        hideCal();
    }

    var setupListeners = function() {
        Event.addListener('cal1Container', 'mouseover', function() {
            over_cal = true;
        });
        Event.addListener('cal1Container', 'mouseout', function() {
            over_cal = false;
        });
    }

    var getDate = function() {
            var calDate = this.getSelectedDates()[0];
            // console.log(calDate);
            // var calDate = new Date();
            // calDate = (calDate.getMonth() + 1) + '/' + calDate.getDate() + '/' + calDate.getFullYear();
            // calDate = calDate.getDate() + '.' + (calDate.getMonth() +1) + '.' + calDate.getFullYear();
            calDate = formatDate(calDate);
            cur_field.value = calDate;
            over_cal = false;
            hideCal();
    }

    var formatDate = function(date) {
            return (date.getDate() + '.' + (date.getMonth() +1) + '.' + date.getFullYear());
    }

    var showCal = function(ev) {
        var tar = Event.getTarget(ev);
        cur_field = tar;
    
        var xy = Dom.getXY(tar),
            date = Dom.get(tar).value;
        // date = new Date();
        if (date) {
            cal1.cfg.setProperty('selected', date);
            cal1.cfg.setProperty('pagedate', new Date(), true);
            // cal1.cfg.setProperty('pagedate', new Date(date), true);
        } else {
            cal1.cfg.setProperty('selected', '');
            // cal1.cfg.setProperty('pagedate', date, true);
            cal1.cfg.setProperty('pagedate', new Date(), true);
        }
        cal1.render();
        Dom.setStyle('cal1Container', 'display', 'block');
        xy[1] = xy[1] + 20;
        Dom.setXY('cal1Container', xy);
    }

    var hideCal = function() {
        if (!over_cal) {
            Dom.setStyle('cal1Container', 'display', 'none');
        }
    }

    initCalendar();

}
