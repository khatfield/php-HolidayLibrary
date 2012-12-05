$(function(){
    
    $("#holiday-picker").datepicker( { minDate: 0, beforeShowDay: noWeekendsOrHolidays } );
    
});

//the script that will give us the holiday dates
var holidayScript = 'get_dates.php';

//Today's date
var nowish        = new Date();

//This year
usHolidays.year   = nowish.getFullYear();

//Set the holidays
setHolidays(usHolidays.year);

//call the PHP script to get the dates for the selected year
function setHolidays(year){
    $.ajax({
        url: holidayScript,
        data: {year: year},
        dataType: 'json',
        async: false, //while this is not generally good practice, it works here
        success: function(data){
            usHolidays.holidays = data;
        }
    });
}

//The function used by the date picker
function noWeekendsOrHolidays(date){
    var noWeekend = $.datepicker.noWeekends(date);
    
    if(noWeekend[0]){
        return usHolidays(date); 
    } else {
        return noWeekend;
    }
}

//Helper function to determine if current date is a holiday
function usHolidays(date){
    var d = date.getDate();
    var m = date.getMonth() + 1;
    var y = date.getFullYear();
    
    if(y != usHolidays.year){
        setHolidays(y);
        usHolidays.year = y;
    }
    
    for(i = 0; i < usHolidays.holidays.length; i++){
        if(m == usHolidays.holidays[i][0] && d == usHolidays.holidays[i][1]){
            return [false, ''];
        }
    }
    
    return [true, ''];
}
