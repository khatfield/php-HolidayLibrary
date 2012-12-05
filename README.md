# Holiday Library for PHP
by [Keith Hatfield](http://keithscode.com)

This library will help with the calculation of Holiday dates for
United States holidays.

## Installation
Include the `Holidays.class.php` file in your PHP script:

    require_once('Holidays.class.php');
    
### Configuration
The class takes 2 optional parameters: year and extra holidays. If omitted, the library will use the current year with no extra holidays.

    //use current year with no extra holidays
    $holidays = new Holidays();

#### Options
* Observance: Uses observed holiday date if holiday falls on the
  weekend (default: true)
* Easter: Option to include Easter in the list of holidays
  (default: false)
* Good Friday: Option to include Good Friday in the list of
  Holidays (default: false)

#### Adding Extra Holidays
The extra array will contain any extra holidays that should be includes. 
There are three different types of days that can go in the array.
All types will be combined in the extra array

* Fixed Days
  * Month and day for the holiday in an array with 2 parameters
  * 1st param: month (1 - 12)
  * 2nd param: day of month (1 - 31)
  * ex. New Year's Eve would be array(12, 31)

* Floating
  * Specific day of the month in an array with 3 parameters
  * 1st param: day of week (0 - 6) 0 = sunday
  * 2nd param: month (1 - 12)
  * 3rd param: week of month (0 - 5) 5 = last week of month (even if the last week may be the 4th week)
  * ex. Last Wednesday of October would be array(3, 10, 5)

* Special
  * Day in relation to another holiday 
  * 1st param: positive or negative day of week (0 - 7) negative values indicate before, positive indicate after, for sunday, 0 = sunday before, 7 = sunday after
  * 2nd param: day of week (0 - 6) for the holiday
  * 3rd param: month (1 - 12)
  * 4th param: week of month (0 - 5) 5 = last week of month (even if the last week may be the 4th week)
  * ex. The friday before Memorial Day would be array(-5, 1, 5, 5) (i.e., the Friday before the last Monday in May)
  * ex. The Friday after Thanksgiving would be array(+5, 4, 11, 4) (i.e., the Friday after the 4th Thursday in November)
  * ex. The Sunday after Thanksgiving would be array(+7, 4, 11, 4) (i.e., the Sunday after the 4th Thursday in November)

* Example Extra Array

        $extra = array(
            "New Year's Eve"          => array(12, 31),
            'Fri. after Thanksgiving' => array(+5, 4, 11, 4),
            'Fri. before Memorial'    => array(-5, 1, 5, 5)
        );
        
        //create object for current year with extra holidays
        $holidays = new Holidays(null, $extra);

## Default Holidays
* New Year's Day   - 1/1
* Independence Day - 7/4
* Veteran's Day    - 11/11
* Christmas Day    - 12/25
* MLK Day          - 3rd Mon of Jan
* Pres Day         - 3rd Mon of Feb
* Memorial         - Last Mon of May
* Labor            - 1st Mon of Sep
* Columbus         - 2nd Mon of Oct 
* Thanksgiving     - 4th Thurs of Nov

## Included Examples
* `examples/datepicker/`: Using the holiday library to exclude holidays from the jQuery UI Datepicker
* `examples/get_year/`: Get holiday dates for a given year. Displays both actual dates and observances. 