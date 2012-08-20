#!/usr/bin/php
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
// we define a valid entry point
if(!defined('__CHRIS_ENTRY_POINT__')) define('__CHRIS_ENTRY_POINT__', 666);
// include the configuration file
// requires the full path
$confFile = dirname(__FILE__).'/../config.inc.php';
if(!defined('CHRIS_CONFIG_PARSED')) require_once($confFile);

// include the controller classes
require_once 'db.class.php';
require_once 'mapper.class.php';
require_once 'pacs.class.php';

// include the model classes
require_once (joinPaths(CHRIS_MODEL_FOLDER, 'patient.class.php'));
require_once (joinPaths(CHRIS_MODEL_FOLDER, 'data.class.php'));


// define command line arguments
$shortopts = "";
$shortopts .= "p:"; // Full path
$shortopts .= "f:"; // File name
$shortopts .= "a:"; // File name
$shortopts .= "c:"; // Optional value

$options = getopt($shortopts);
//var_dump($options);

/* PACS::process($options['p'].'/'.$options['f']); */
 $p = $options['p'];
$f = $options['f']; /*
 $p = '/chb/users/chris/tmp/RX_20120815_143408184'; //$options['p']
 $f = 'US.1.2.840.113619.2.256.896737926219.1336499244.3424'; //$options['f'] */
$tmpfile = $p.'/'.$f;

$result = PACS::process($tmpfile);
echo '<br/>';
$patient_chris_id = -1;
$data_chris_id = -1;
$image_chris_id = -1;
$protocol_name = 'NoProtocolName';

// parse results patient first
// Does patient exist
// Unique Name/Birthdate combination
/* if (array_key_exists('PatientName',$result) && array_key_exists('PatientBirthdate',$result))
 */
if (array_key_exists('PatientName',$result))
{
  $patientMapper = new Mapper('Patient');
  $patientMapper->filter('name = (?)',$result['PatientName'][0] );

  /*   $dob = $result['PatientBirthdate'][0];
   $patientMapper->filter('dob = (?)',$dob ); */

  $patientResult = $patientMapper->get();

  if(count($patientResult['Patient']) == 0)
  {
    echo 'Patient NOT in DB';
    echo '<br/>';
    // create patient model
    $patientObject = new Patient();
    $patientObject->name = $result['PatientName'][0];
    $patientObject->dob = '0000-00-00';
    $patientObject->sex = $result['PatientSex'][0];
    $patientObject->patient_id = $result['PatientID'][0].';';

    // add the patient model and get its id
    $patient_chris_id = Mapper::add($patientObject);
  }
  else {
    echo 'Patient ALREADY in DB';
    echo '<br/>';
    // get patient id
    $patient_chris_id = $patientResult['Patient'][0]->id;

    // update MRN field if MRN provided
    if(array_key_exists('PatientID',$result)){
      $patient_mrn = $patientResult['Patient'][0]->patient_id;
      $list_patient_mrn = explode(';', $patient_mrn);
      // and if not already there...!
      if(!in_array($result['PatientID'][0], $list_patient_mrn)){
        // create patient model
        $patientObject = new Patient();
        $patientObject->name = $patientResult['Patient'][0]->name;
        $patientObject->dob = $patientResult['Patient'][0]->dob;
        $patientObject->sex = $patientResult['Patient'][0]->sex;
        // previous MRN list
        $patientObject->patient_id = $patientResult['Patient'][0]->patient_id;
        // add new MRN
        $patientObject->patient_id .= $result['PatientID'][0].';';

        Mapper::update($patientObject, $patient_chris_id);
      }
    }
  }

  echo $patient_chris_id;
  echo '<br/>';
}
else {
  echo 'PatientName or PatientBirthdate not there';
  echo '<br/>';
  return;
}

// Does Image exist: SOPInstanceUID
if (array_key_exists('SeriesInstanceUID',$result))
{
  // does data (series) exist??
  $dataMapper = new Mapper('Data');
  $value = $result['SeriesInstanceUID'][0];
  $dataMapper->filter('unique_id = (?)',$value );
  $dataResult = $dataMapper->get();

  // if doesnt exist, create data
  if(count($dataResult['Data']) == 0)
  {
    echo 'DATA NOT IN DB ';
    echo '<br/>';
    print_r($result);
    // create data model
    $dataObject = new Data();
    $dataObject->patient_id = $patient_chris_id;
    $dataObject->unique_id = $result['SeriesInstanceUID'][0];
    // remove potential white spaces
    if(array_key_exists('ProtocolName',$result))
    {
      $protocol_name = str_replace (' ', '_', $result['ProtocolName'][0]);
    }
    $dataObject->name = $protocol_name;
    $dataObject->time = $result['ContentTime'][0];
    $dataObject->meta_information = '';

    // add the data model and get its id
    $data_chris_id = Mapper::add($dataObject);
  }
  // else get data id
  else{
    echo 'DATA ALREADY IN DB ';
    echo '<br/>';
    $data_chris_id = $dataResult['Data'][0]->id;
  }
  echo $data_chris_id;
  echo '<br/>';

}
else {
  echo 'SOPInstanceUID or SeriesInstanceUID not there';
  echo '<br/>';
  return;
}

// FILESYSTEM STUFF
$patientdirname = '/chb/users/chris/data/'.$result['PatientID'][0].'-'.$patient_chris_id;
echo $dirname;
echo '<br/>';
// create folder if doesnt exists
if(!is_dir($patientdirname)){
  mkdir($patientdirname);
  echo 'Create DIR';
}
else{
  echo 'DIR already there';
}

$datadirname = $patientdirname.'/'.$protocol_name.'-'.$data_chris_id;
echo $datadirname;
echo '<br/>';
// create folder if doesnt exists
if(!is_dir($datadirname)){
  mkdir($datadirname);
  echo 'Create DIR';
}
else{
  echo 'DIR already there';
}



echo '<br/>';

// cp file over if doesnt exist
$filename = $datadirname .'/'.$f;
echo $filename;
echo '<br/>';
echo $tmpfile;
echo '<br/>';
if(!is_file($filename)){
  echo 'Move IMAGE';
  copy($tmpfile, $filename);
}
else{
  echo 'IMAGE already there';
}
echo '<br/>';

// keep it for testing
echo 'delete: '.$filename.' - NOT HOOKED';
echo '<br/>';
//delete($options['p'].'/'.$options['f']);
?>