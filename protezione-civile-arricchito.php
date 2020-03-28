//legge da github protezione civile, aggiunge num di residenti per provincia e calcola delta e incrementi %
//risultato di qua: https://datastudio.google.com/reporting/1c1acdbb-f8a5-4d61-ace2-bd30fab70dc2/page/dDZIB


$arrayResidenti = array('3' => '10018806',
                        '12'=>'5898124',
                        '15'=>'5839084',
                        '19'=>'5056641',
                        '5'=>'4907529',
                        '8'=>'4448841',
                        '1'=>'4392526',
                        '16'=>'4063888',
                        '9'=>'3742437',
                        '18'=>'1965128',
                        '20'=>'1653135',
                        '7'=>'1565307',
                        '11'=>'1538055',
                        '13'=>'1322247',
                        '6'=>'1217872',
                        '4'=>'1062860',
                        '10'=>'888908',
                        '17'=>'570365',
                        '14'=>'310449',
                        '2'=>'126883');        

$raw = file_get_contents('https://raw.githubusercontent.com/pcm-dpc/COVID-19/master/dati-json/dpc-covid19-ita-regioni.json');
$json = json_decode($raw);

foreach($json as $item) {            
    $data = date('d/m/Y', strtotime($item->data));            
    
    $nuovi_ricoverati_con_sintomi = 0;
    $nuovi_terapia_intensiva = 0;
    $nuovi_ospedalizzati = 0;
    $nuovi_isolamento_domiciliare = 0;            
    $nuovi_dimessi_guariti = 0;
    $nuovi_deceduti = 0;
    $nuovi_casi = 0;     
    $nuovi_tamponi = 0;

    $inc_perc_casi = 0;
    $inc_perc_attualmente_positivi = 0;            
    $inc_perc_deceduti = 0;
    $inc_perc_dimessi_guariti = 0;
    $inc_perc_ospedalizzati = 0;

    //CERCO GIORNO PRECEDENTE
    if($data != "24/02/2020") {                
        $ieri=date('d/m/Y', strtotime($item->data. ' - 1 day'));                
        foreach($json as $itemCheck) {
            $dataCheck = date('d/m/Y', strtotime($itemCheck->data));
            if($dataCheck == $ieri && $item->codice_regione == $itemCheck->codice_regione && $item->denominazione_regione == $itemCheck->denominazione_regione) {                           
                $nuovi_ricoverati_con_sintomi = $item->ricoverati_con_sintomi - $itemCheck->ricoverati_con_sintomi;
                $nuovi_terapia_intensiva = $item->terapia_intensiva - $itemCheck->terapia_intensiva;
                $nuovi_ospedalizzati = $item->totale_ospedalizzati - $itemCheck->totale_ospedalizzati;
                $nuovi_isolamento_domiciliare = $item->isolamento_domiciliare - $itemCheck->isolamento_domiciliare;
                $nuovi_dimessi_guariti = $item->dimessi_guariti - $itemCheck->dimessi_guariti;
                $nuovi_deceduti = $item->deceduti - $itemCheck->deceduti;                        
                $nuovi_casi = $item->totale_casi - $itemCheck->totale_casi;
                $nuovi_tamponi = $item->tamponi - $itemCheck->tamponi;

                if($itemCheck->nuovi_casi)
                    $inc_perc_casi = ($nuovi_casi - $itemCheck->nuovi_casi) / $itemCheck->nuovi_casi;

                if($itemCheck->nuovi_attualmente_positivi)
                    $inc_perc_attualmente_positivi = ($item->nuovi_attualmente_positivi - $itemCheck->nuovi_attualmente_positivi) / $itemCheck->nuovi_attualmente_positivi;

                if($itemCheck->nuovi_deceduti)
                    $inc_perc_deceduti = ($nuovi_deceduti - $itemCheck->nuovi_deceduti) / $itemCheck->nuovi_deceduti;

                if($itemCheck->nuovi_dimessi_guariti)
                    $inc_perc_dimessi_guariti = ($nuovi_dimessi_guariti  - $itemCheck->nuovi_dimessi_guariti) / $itemCheck->nuovi_dimessi_guariti;

                if($itemCheck->nuovi_ospedalizzati)
                    $inc_perc_ospedalizzati = ($nuovi_ospedalizzati  - $itemCheck->nuovi_ospedalizzati) / $itemCheck->nuovi_ospedalizzati;
                
                $item->nuovi_ricoverati_con_sintomi_giorno_prec = $itemCheck->nuovi_ricoverati_con_sintomi;
                $item->nnuovi_terapia_intensiva_giorno_prec = $itemCheck->nuovi_terapia_intensiva;
                $item->nuovi_ospedalizzati_giorno_prec = $itemCheck->nuovi_ospedalizzati;
                $item->nuovi_isolamento_domiciliare_giorno_prec = $itemCheck->nuovi_isolamento_domiciliare;
                $item->nuovi_dimessi_guariti_giorno_prec = $itemCheck->nuovi_dimessi_guariti;
                $item->nuovi_deceduti_giorno_prec = $itemCheck->nuovi_deceduti;
                $item->nuovi_casi_giorno_prec = $itemCheck->nuovi_casi;
                $item->nuovi_tamponi_giorno_prec = $itemCheck->nuovi_tamponi;
                    
                break;
            }
        }
    }

    if(isset($arrayResidenti[$item->codice_regione])) {
        $item->residenti = (int) $arrayResidenti[$item->codice_regione];
    }

    $item->nuovi_ricoverati_con_sintomi = $nuovi_ricoverati_con_sintomi;
    $item->nuovi_terapia_intensiva = $nuovi_terapia_intensiva;
    $item->nuovi_ospedalizzati = $nuovi_ospedalizzati;
    $item->nuovi_isolamento_domiciliare = $nuovi_isolamento_domiciliare;
    $item->nuovi_dimessi_guariti = $nuovi_dimessi_guariti;
    $item->nuovi_deceduti = $nuovi_deceduti;
    $item->nuovi_casi = $nuovi_casi;
    $item->nuovi_tamponi = $nuovi_tamponi;


    $item->inc_perc_casi = $inc_perc_casi;
    $item->inc_perc_attualmente_positivi = $inc_perc_attualmente_positivi;
    $item->inc_perc_deceduti = $inc_perc_deceduti;
    $item->inc_perc_dimessi_guariti = $inc_perc_dimessi_guariti;
    $item->inc_perc_ospedalizzati = $inc_perc_ospedalizzati;

    $item->data = date('Y-m-d h:i:s', strtotime($item->data));

}

echo json_encode($json);