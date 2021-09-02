<?php
require_once('../libs/vendor/autoload.php');

function printPdf($html){

    try {

      $tempDir = '../temp/default/pdf';

      $mpdf = new \Mpdf\Mpdf([
        'mode' => false,
        'format' => $PDF_LANDSCAPE?"A4-L":"A4",
        'default_font_size' => false,
        'default_font' => 'Arial',
        'margin_left' => $PDF_MARGIN_LEFT,
        'margin_right' => $PDF_MARGIN_RIGHT,
        'margin_top' => $PDF_MARGIN_TOP,
        'margin_bottom' => $PDF_MARGIN_BOTTOM,

        'tempDir' => $tempDir,
        'setAutoTopMargin' => 'stretch',
        'setAutoBottomMargin' => 'stretch'
      ]);

        $fileStyle1 = __DIR__ . "../../../skins/".$CURRENT_SKIN."/css/pdf.css";

        $fileStyle = "../skins/".$CURRENT_SKIN."/css/pdf.css";

              $stylesheet = file_get_contents($fileStyle1);

              $mpdf->WriteHTML($stylesheet, 1);
              $mpdf->WriteHTML($html,2);
              $mpdf->Output('pdf_departments.pdf', 'I');
              $mpdf->Output();

    } catch (\Mpdf\MpdfException $e) {
        print "Creating an PDF object failed with" . $e->getMessage();
    }

}