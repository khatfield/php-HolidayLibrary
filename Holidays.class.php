<?php
/**
 * Holidays Library
 * 
 * Calculates holiday dates for a given year
 * 
 * Fixed Holidays:
 *   New Year's Day   - 1/1
 *   Independence Day - 7/4
 *   Veteran's Day    - 11/11
 *   Christmas Day    - 12/25
 * 
 * Floating Holidays
 *   MLK Day      - 3rd Mon of Jan
 *   Pres Day     - 3rd Mon of Feb
 *   Memorial     - Last Mon of May
 *   Labor        - 1st Mon of Sep
 *   Columbus     - 2nd Mon of Oct 
 *   Thanksgiving - 4th Thurs of Nov
 * 
 * 
 * @author Keith Hatfield <keith.hatfield@gmail.com>
 *         @keithscode
 *
 */
class Holidays implements Iterator
{
    /**
     * Fixed Holidays
     *
     * @var array
     */
    private $fixed = array(
        "New Year's Day"   => array(1,1),
        "Independence Day" => array(7,4),
        "Veteran's Day"    => array(11,11),
        "Christmas"        => array(12,25)
    );

    /**
     * Floating Holidays
     *
     * @var array
     */
    private $float = array(
        "MLK Day"          => array(1, 1, 3),
        "President's Day"  => array(1, 2, 3),
        "Memorial Day"     => array(1, 5, 5),
        "Labor Day"        => array(1, 9, 1),
        "Columbus Day"     => array(1, 10, 2),
        "Thanksgiving"     => array(4, 11, 4)
    );

    /**
     * Special Holidays
     * (See config file for more details)
     *
     * @var array
     */
    private $spec   = array();

    /**
     * The year to calcualte for
     * Defaults to current year at initialization
     *
     * @var integer
     */
    private $year;

    /**
     * Use observances if Holiday falls on weekend
     * Defaults to true
     *
     * @var bool
     */
    private $observance  = true;

    /**
     * Include Easter
     * Defaults to false
     *
     * @var bool
     */
    private $easter      = false;

    /**
     * Include Good Friday
     * Defaults to false
     *
     * @var bool
     */
    private $good_friday = false;

    /**
     * Holidays Array
     *
     * @var array
     */
    private $holidays    = array();

    /**
     * Constructor
     * 
     * Sets the variables from the config file and does the initial calculation
     *
     * @param array $config Configuration info from the config file at config/Holidays.php
     */
    public function __construct($year = null, $extra = null)
    {
        if(is_null($year) || !is_numeric($year)){
            $year   = date('Y');
        }
        $this->year = $year;

        if(!is_null($extra)){
            $this->setExtras($extra);
        }

        $this->calcHolidays();
    }

    /**
     * Public method to set the extra days from the config file
     *
     * @param array $extras 
     */
    public function setExtras($extras = array())
    {
        if(!empty($extras)){
            foreach($extras as $name => $data){
                if(count($data) == 2){
                    $this->fixed[$name] = $data;
                }
                if(count($data) == 3){
                    $this->float[$name] = $data;
                }
                if(count($data) == 4){
                    $this->spec[$name]  = $data;
                }
            }
        }

        $this->calcHolidays();

        return true;
    }

    /**
     * Setter for Good Friday Option
     * 
     * @param bool $include Value to set for this option
     */
    public function setGoodFriday($include = false)
    {
        $this->good_friday  = $include;
        
        $this->calcHolidays();
    }

    /**
     * Setter for Easter Option
     * 
     * @param bool $include Value to set for this option
     */
    public function setEaster($include = false)
    {
        $this->easter      = $include;
        
        $this->calcHolidays();
    }

    /**
     * Setter for Observance Option
     * 
     * @param bool $include Value to set for this option
     */
    public function setObservances($include = true)
    {
        $this->observance  = $include;
        
        $this->calcHolidays();
    }

    /**
     * Setter for the year
     * 
     * Sets the year and recalculates the holidays
     * 
     * @param int $year Int year value
     */
    public function setYear($year)
    {
        if($this->year != $year){
            $this->year = $year;
            $this->calcHolidays();
        }
    }

    /**
     * Getter for Holiday array
     * 
     * @return array Local holiday array
     */
    public function getHolidays()
    {
        return $this->holidays;
    }

    /**
     * Get the holiday array in JSON format
     * 
     * This is useful for passing to the jQuery date picker to exclude holiday dates
     * 
     * @return string JSON encoded holiday array in a format compatible with jQuery Date Picker
     */
    public function getJson()
    {
        foreach($this->holidays as $holiday){
            $return[] = array( date('n',strtotime($holiday)), date('j',strtotime($holiday)) );
        }
        
        return json_encode($return);
    }

    /**
     * Get the date for a floating holiday
     * 
     * @access private
     * @param  array  $data    The data for the floating holiday
     * @return string Holiday date in Y-m-d format
     */
    private function getFloatDate($data)
    {
        $dow   = $data[0];
        $month = $data[1];
        $week  = $data[2];
        $last  = false;
        if($week > 4){
            $week = 4;
            $last = true;
        }

        $earliest = (7 * ($week - 1)) + 1;
        $weekday  = date('w', strtotime($this->year . '-' . $month . '-'. $earliest));

        $offset = 0;
        if($dow < $weekday){
            $offset = $dow + 7 - $weekday;
        } else {
            $offset = $dow - $weekday;
        }

        $target = date('Y-m-d', strtotime($this->year . '-' . $month . '-'. ($earliest + $offset)));

        //check for 'last x of month' in case of 5 weeks ... 
        if($last){
            $first = $this->year . '-' . $month . '-01';
            $f_dow = date('w', strtotime($first));
            $days  = date('t', strtotime($first));

            $extra = array();
            for($i = 0; $i < ($days - 28); $i++){
                $day = $f_dow + $i;
                if($day > 6){
                    $day -= 7;
                }
                $extra[] = $day;
            }
            if(in_array($dow, $extra)){
                $target = date('Y-m-d', strtotime('+1 week', strtotime($target)));
            }
        }

        return $target;
    }

    /**
     * Calulates the Holiday Dates and loads the member array
     *
     * @access private
     */
    private function calcHolidays()
    {
        $this->holidays = array();

        //check the fixed holidays ...
        foreach($this->fixed as $name => $data){

            $target = $this->year . '-' . sprintf('%02s',$data[0]) . '-' . sprintf('%02s',$data[1]);
            if($this->observance){
                $dow    = date('w', strtotime($target));
                if($dow == 0){
                    $target = date('Y-m-d', strtotime('+1 day', strtotime($target)));
                    $name .= '*';
                }
                if($dow == 6){
                    $target = date('Y-m-d', strtotime('-1 day', strtotime($target)));
                    $name .= '*';
                }
            }

            $this->holidays[$name] = $target;
        }

        //process the floating holidays
        foreach($this->float as $name => $data){
            $target = $this->getFloatDate($data);

            $this->holidays[$name] = $target;
        }

        //process the special holidays
        foreach($this->spec as $name => $data){
            $dow    = $data[0];
            $h_dow  = $data[1];
            $float  = array_slice($data, 1);

            $target = $this->getFloatDate($float);

            //calculate the offset for the special day ... 
            if($dow < 0){
                if(abs($dow) < $h_dow){
                    $diff = ($h_dow + $dow) * -1;
                } else {
                    $diff = (7 + $dow + $h_dow) * -1;
                }
            } else {
                $diff = $dow - $h_dow;
                if($diff < 0){
                    $diff += 7;
                }
            }

            $target = date('Y-m-d', strtotime($diff . ' day', strtotime($target)));

            $this->holidays[$name] = $target;
        }

        //add easter
        if($this->easter){
            $this->holidays['Easter'] = date('Y-m-d', easter_date($this->year));
        }

        //add good friday
        if($this->good_friday){
            $this->holidays['Good Friday'] = date('Y-m-d', easter_date($this->year) - 2 * 24 * 60 * 60);
        }

        //sort by date
        asort($this->holidays);
        
        $this->rewind();
    }

    // Iterator functions
    public function current()
    {
        return current($this->holidays);
    }

    public function key()
    {
        return key($this->holidays);
    }

    public function next()
    {
        return next($this->holidays);
    }

    public function rewind()
    {
        return reset($this->holidays);
    }

    public function valid()
    {
        return !is_null(key($this->holidays));
    }
}