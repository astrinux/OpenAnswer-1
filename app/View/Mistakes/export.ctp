<?php
$row = array('"Message Date"', '"Account Num"', '"Message #"', '"Category"', '"Issued by"', '"Recipient"', '"Details"', '""');
$data[] = $row;
$row_empty = array('','','','');
$rowdef[0] = array('"Created"', '"Operator"', '"Highs"', '"Lows"', '"Observations"');
$rowdef[1] = array('"Created"', '"Operator"', '"Highs"', '"Lows"', '"Feedback"', '"Observations"');
$rowdef[2] = array('"Created"', '"Operator"', '"Highs"', '"Lows"', '"Participant/General Feedback"', '"Accomplishments"');

$col_delimiter = ",";
$fileName = "WeeklyReport_".$report_date.".csv";
$form_type = '';
foreach ($OperatorReports as $s) {
  if ($form_type != $s['OperatorReport']['form_type']) {
    $data[] = $row_empty;
    $data[] = $rowdef[$s['OperatorReport']['form_type']];
  }
  $form_type = $s['OperatorReport']['form_type'];
  $row[0] = '"' . $s['OperatorReport']['created']. '"';
  $row[1] = '"' . $operators[$s['OperatorReport']['operator_code']]. '"';
  $row[2] = '"' . str_replace('"', "'", $s['OperatorReport']['q1']) . '"';
  $row[3] = '"' . str_replace('"', "'", $s['OperatorReport']['q2']) . '"';
  $row[4] = '"' . str_replace('"', "'", $s['OperatorReport']['q3']) . '"';
  $data[] = $row;
}
header("Content-type: text/csv"); 
header("Content-Disposition: attachment; filename=$fileName");
$tsv = array();
foreach ($data as $d) {
   $tsv[]  = implode($col_delimiter, $d);
}

echo implode("\r\n", $tsv);
?>
