<?php 


include("mpdf.php");

$mpdf = new mPDF('c');

//$stylesheet = file_get_contents('pdf.css'); // external css
$html= '<div class="test" >Section 11111111111111111111111111111111 text</div>';

//$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($html,2);


$mpdf->Output('test.pdf','I'); 

exit;


?>