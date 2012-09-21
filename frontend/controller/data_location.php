<?php
/**
 *
 *            sSSs   .S    S.    .S_sSSs     .S    sSSs
 *           d%%SP  .SS    SS.  .SS~YS%%b   .SS   d%%SP
 *          d%S'    S%S    S%S  S%S   `S%b  S%S  d%S'
 *          S%S     S%S    S%S  S%S    S%S  S%S  S%|
 *          S&S     S%S SSSS%S  S%S    d* S  S&S  S&S
 *          S&S     S&S  SSS&S  S&S   .S* S  S&S  Y&Ss
 *          S&S     S&S    S&S  S&S_sdSSS   S&S  `S&&S
 *          S&S     S&S    S&S  S&S~YSY%b   S&S    `S*S
 *          S*b     S*S    S*S  S*S   `S%b  S*S     l*S
 *          S*S.    S*S    S*S  S*S    S%S  S*S    .S*P
 *           SSSbs  S*S    S*S  S*S    S&S  S*S  sSS*S
 *            YSSP  SSS    S*S  S*S    SSS  S*S  YSS'
 *                         SP   SP          SP
 *                         Y    Y           Y
 *
 *                     R  E  L  O  A  D  E  D
 *
 * (c) 2012 Fetal-Neonatal Neuroimaging & Developmental Science Center
 *                   Boston Children's Hospital
 *
 *              http://childrenshospital.org/FNNDSC/
 *                        dev@babyMRI.org
 *
 */
define('__CHRIS_ENTRY_POINT__', 666);

// include the configuration
require_once ($_SERVER['DOCUMENT_ROOT_NICOLAS'].'/config.inc.php');
require_once 'db.class.php';
require_once 'mapper.class.php';
require_once 'pacs.class.php';

// include the models
require_once (joinPaths(CHRIS_MODEL_FOLDER, 'data.model.php'));

// retrieve the data
$dataMapper = new Mapper('Data');
$dataMapper->filter('unique_id = (?)',$_POST['DATA_SER_UID']);
$dataResult = $dataMapper->get();

// if nothing in DB yet, return null
if(count($dataResult['Data']) == 0)
{
  echo json_encode('');
  return;
}

// get data and patient UIDs
$data_uid = $dataResult['Data'][0]->id;
$patient_uid = $dataResult['Data'][0]->patient_id;

// find the data location
// find patient
$patient_entry = '';
if ($handle = opendir(CHRIS_DATA)) {
  while (false !== ($entry = readdir($handle))) {
    if($entry != "." && $entry != ".."){
      if (preg_match('/\-'.$patient_uid.'$/', $entry)) {
        // patient directory
        $patient_entry = $entry;
        break;
      }
    }
  }
  closedir($handle);
}

// find data
$data_entry = '';
if ($handle2 = opendir(CHRIS_DATA.$patient_entry)) {
  while (false !== ($entry2 = readdir($handle2))) {
    if($entry2 != "." && $entry2 != ".."){
      if (preg_match('/\-'.$data_uid.'$/', $entry2)) {
        $data_entry = $entry2;
        break;
      }
    }
  }
  closedir($handle2);
}

echo json_encode(CHRIS_DATA.$patient_entry.'/'.$data_entry);
?>