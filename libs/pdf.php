<?php
abstract Class PDF
{
	public static function get_pdf_head()
	{
        if (isset($GLOBALS['PDF_LOGO'])){
            $PdfLogo = $GLOBALS['PDF_LOGO'];
        }else{
            $PdfLogo="../skins/".$GLOBALS['CURRENT_SKIN']."/images/pdf/logo.png";
        }


		return "
		<table id=\"header\">
		<tr>
		<td rowspan=\"3\"><img src=\"".$PdfLogo."\" width=\"".$GLOBALS['PDF_WIDTH_LOGO']."\" height=\"".$GLOBALS['PDF_HEIGHT_LOGO']."\"></td>
		<td id=\"title\">".$GLOBALS['PDF_TITLE']." (".$GLOBALS['BOOKMARK_NAMES'][$GLOBALS['bookmark_attr']][$GLOBALS['BOOKMARK_NAME']].")</td>
		</tr>
		<tr>
		<td id=\"second_life\">".$GLOBALS['PDF_SECOND_LINE']."</td>
		</tr>
		<tr>
		<td id=\"create_date\">".date("d.m.Y")."</td>
		</tr>
		</table>";
	}
}
?>