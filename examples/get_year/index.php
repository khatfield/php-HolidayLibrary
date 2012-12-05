<!DOCTYPE html>
<html>
    <head>
        <title>Year Example</title>
        <style>
            body{
                font-family: verdana;
                font-size: 80%;
            }
            
            table{
                width: 400px;
                border-collapse: collapse;
                border: 1px solid #666;
            }
            
            tr{
                border-bottom: 1px solid #ccc;
            }
            
            tr:last-child{
                border-bottom: 0;
            }
            
            h1{
                font-size: 1.5em;
            }
            
            h2{
                font-size: 1.4em;
            }
        </style>
    </head>
    <body>
        <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
            <input type="text" name="year" id="year" />
            <input type="submit" name="submit" value="Go" />
        </form>
        <hr />
<?php 
    if($_SERVER['REQUEST_METHOD'] == 'POST'):
        $year = $_REQUEST['year'];
        if(empty($year) || !is_numeric($year)):
?>
            <p>Invalid Date Input</p>
<?php
        else:
            require_once('../../Holidays.class.php');
            $holidays = new Holidays($year);
?>
        <h1>Holidays for <?= $year; ?></h1>
        <h2>Observed Dates</h2>
        <table>
            <tr>
                <th>Holiday</th>
                <th>Observed Date</th>
            </tr>
            <?php foreach($holidays as $name => $date): ?>
            <tr>
                <td><?= $name; ?></td>
                <td><?= date('l, F j, Y', strtotime($date)); ?></td>
            </tr>    
            <?php endforeach; ?>
        </table>
        
        <?php $holidays->setObservances(false); ?>
        <h2>Actual Dates</h2>
        <table>
            <tr>
                <th>Holiday</th>
                <th>Date</th>
            </tr>
            <?php foreach($holidays as $name => $date): ?>
            <tr>
                <td><?= $name; ?></td>
                <td><?= date('l, F j, Y', strtotime($date)); ?></td>
            </tr>    
            <?php endforeach; ?>
        </table>
        
<?php            
        endif;
    endif; 
?>
    </body>
</html>