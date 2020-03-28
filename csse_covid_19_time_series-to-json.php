
//legge da github Johns Hopkins CSSE https://github.com/CSSEGISandData/COVID-19 e mette insieme i csv con timeseries
//risultato di qua: https://datastudio.google.com/reporting/1c1acdbb-f8a5-4d61-ace2-bd30fab70dc2/page/dDZIB


//LEGGO CONFERMATI
$raw_confirmed = file_get_contents('https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_covid19_confirmed_global.csv');

$data_confirmed = array();
$head_confirmed = null;

$data = str_getcsv($raw_confirmed, "\n"); //parse the rows
$index = 0;
foreach ($data as &$row) {
    $row = str_getcsv($row, ","); //parse the items in rows 
    if($index == 0) 
        $head_confirmed = $row;
    else
        $data_confirmed[] = $row;

    $index++;
}

$raw_deaths = file_get_contents('https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_covid19_deaths_global.csv');

$data_deaths = array();
$head_deaths = null;

$data = str_getcsv($raw_deaths, "\n"); //parse the rows
$index = 0;
foreach ($data as &$row) {
    $row = str_getcsv($row, ","); //parse the items in rows 
    if($index == 0) 
        $head_deaths = $row;
    else
        $data_deaths[] = $row;

    $index++;
}

$raw_recovered = file_get_contents('https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_covid19_recovered_global.csv');

$data_recovered = array();
$head_recovered = null;

$data = str_getcsv($raw_recovered, "\n"); //parse the rows
$index = 0;
foreach ($data as &$row) {
    $row = str_getcsv($row, ","); //parse the items in rows 
    if($index == 0) 
        $head_recovered = $row;
    else
        $data_recovered[] = $row;

    $index++;
}


$data_out = array();

//RADDRIZZO CONFERMATI
foreach($data_confirmed as $key => $row) {
    for($i=4; $i<count($row); $i++) {
        if(isset($row[$i]) && isset($head_confirmed[$i])) {       
            $confirmed = intval($row[$i]);             
            $confirmed_prev = 0;
            $new_confirmed = 0;
            $new_confirmed_prev = 0;

            if(isset($row[$i-1]) && $i - 1 > 3) {
                $confirmed_prev = $row[$i-1];
                $new_confirmed = $confirmed - $confirmed_prev;
            }
            if(isset($row[$i-2]) && $i - 2 > 3) {                        
                $new_confirmed_prev = $confirmed_prev - $row[$i-2];
            }
            
            $death = 0;
            $death_prev = 0;
            $new_death = 0;
            $new_death_prev = 0;
            foreach($data_deaths as $row_death) {
                if($row_death[0] == $row[0] && $row_death[1] == $row[1] && isset($row_death[$i])) {
                    $death = intval($row_death[$i]);
                    if(isset($row_death[$i-1]) && $i - 1 > 3) {
                        $death_prev = $row_death[$i-1];
                        $new_death = $death - $death_prev;
                    }
                    if(isset($row_death[$i-2]) && $i - 2 > 3) {                        
                        $new_death_prev = $death_prev - $row_death[$i-2];
                    }
                    break;
                }
            }
            
            $recovered = 0;
            $recovered_prev = 0;
            $new_recovered = 0;
            $new_recovered_prev = 0;
            foreach($data_recovered as $row_recovered) {
                if($row_recovered[0] == $row[0] && $row_recovered[1] == $row[1] && isset($row_recovered[$i])) {
                    $recovered = intval($row_recovered[$i]);
                    if(isset($row_recovered[$i-1]) && $i - 1 > 3) {
                        $recovered_prev = $row_recovered[$i-1];
                        $new_recovered = $recovered - $recovered_prev;
                    }
                    if(isset($row_recovered[$i-2]) && $i - 2 > 3) {                        
                        $new_recovered_prev = $recovered_prev - $row_recovered[$i-2];
                    }
                    break;
                }
            }
            

            $data_out[] = array(    'State' => $row[0], 
                                    'Country' => $row[1], 
                                    'Date' => date('Y-m-d', strtotime($head_confirmed[$i])), 
                                    'Confirmed' => $confirmed, 
                                    'ConfirmedPrevious' => $confirmed_prev,
                                    'Deaths' => $death, 
                                    'DeathsPrevious' => $death_prev, 
                                    'Recovered' => $recovered,
                                    'RecoveredPrevious' => $recovered_prev,
                                    'NewConfirmed' => $new_confirmed,       
                                    'NewConfirmedPrevious' => $new_confirmed_prev,                                            
                                    'NewDeath' => $new_death,                                            
                                    'NewDeathPrevious' => $new_death_prev,     
                                    'NewRecovered' => $new_recovered,       
                                    'NewRecoveredPrevious' => $new_recovered_prev,                                       
                                );
        }
    }
}

echo json_encode($data_out);
    