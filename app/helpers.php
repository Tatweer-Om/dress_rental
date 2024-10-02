<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

function get_date_only($timestamp)
{
    // Create a DateTime object from the timestamp
    $dateTime = new DateTime($timestamp);

    // Format the date as YYYY-MM-DD
    $dateOnly = $dateTime->format('Y-m-d');

    return $dateOnly;
}
function getColumnValue($table, $columnToSearch, $valueToSearch, $columnToRetrieve)
{
    $result = DB::table($table)
                ->where($columnToSearch, $valueToSearch)
                ->first();

    if ($result) {
        return $result->{$columnToRetrieve};
    }

    return 'n/a'; // or any default value you prefer
}
function get_date_time($timestamp)
{
    // Create a DateTime object from the timestamp
    $dateTime = new DateTime($timestamp);

    // Format the date as YYYY-MM-DD
    $formattedDateTime = $dateTime->format('Y-m-d h:i A');

    return $formattedDateTime;
}


function image_preview($path = null)
{
    $images = '';

    // Check if path exists and get the list of files from the directory
    $fullPath = $path; // Convert to public path
    
    if (is_dir($fullPath)) {
        $files = File::files($fullPath); // Fetch files using File facade
        
        foreach ($files as $file) {
            // Get the file extension
            $ext = pathinfo($file, PATHINFO_EXTENSION);

            // Generate the URL for the file
            $url = asset('custom_images/temp_data/' . basename($file));

            // Generate the HTML for each image
            $images .= '
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12"> 
                    <img class="img-thumbnail mb-1" src="' . $url . '" style="max-height:60px !important;min-height:60px !important;max-width:60px;min-width:60px;"> 
                    <p class="text-center">
                        <a href="#" class="card-link rmv-attachment" data-file="' . $url . '">
                            <i class="fa fa-times"></i>
                        </a>
                    </p> 
                </div>';
        }
    }
    return $images;
}

function get_booking_number()
{
    // order no
    $booking_data = DB::table('bookings')->orderBy('id', 'desc')->first();
     


    if($booking_data)
    {
        $booking_no_old = ltrim($booking_data->booking_no, '0');
    }
    else
    {
        $booking_no_old=0;
    }

    $booking_no = $booking_no_old+1;
    $booking_no = '0000000'.$booking_no;
    if(strlen($booking_no)!=8)
    {
        $len = (strlen($booking_no)-8);
        $booking_no = substr($booking_no,$len);
    }
    return $booking_no;
}

function calculateDays($rent_date, $return_date) {
    // Convert the input strings to DateTime objects
    $rentDate = new DateTime($rent_date);
    $returnDate = new DateTime($return_date);

    // Check if the return date is after the rent date
    if ($returnDate > $rentDate) {
        // Calculate the difference in days
        $interval = $rentDate->diff($returnDate);
        $daysDiff = $interval->days; // Get the difference in days

        return $daysDiff; // Return the number of days difference
    } else {
        return 1; // Return 1 if return date is not greater than rent date
    }
}


?>
