<?php
use ru860e\rest\Application;
use ru860e\rest\LDAP;
use ru860e\rest\Staff;

require_once('../libs/mpdf/mpdf_7.0.3/vendor/autoload.php');
require_once('../config.php');
require_once("../libs/forms.php");
require_once("../libs/staff.php");
require_once("../libs/phones.php");
require_once("../libs/pdf.php");
if($ENABLE_PDF_EXPORT)
	{
	Application::makeLdapConfigAttrLowercase();
	$menu_marker="si_export_pdf_department";
	@$BOOKMARK_NAME=($_POST['bookmark_name'])?$_POST['bookmark_name']:(($_GET['bookmark_name'])?$_GET['bookmark_name']:current(array_keys($BOOKMARK_NAMES[current(array_keys($BOOKMARK_NAMES))])) );
	@$bookmark_attr=($_POST['bookmark_attr'])?$_POST['bookmark_attr']:(($_GET['bookmark_attr'])?$_GET['bookmark_attr']:current(array_keys($BOOKMARK_NAMES)));
    $html="";
    $html.=PDF::get_pdf_head();

	$html.="
	<table cellpadding='0' border='0' cellspacing='0' class='staff'>
	";

	$ldap=new LDAP($LDAPServer, $LDAPUser, $LDAPPassword);
	$CompanyNameLdapFilter=Application::getCompanyNameLdapFilter();

	if($USE_DISPLAY_NAME)
		$DisplayName=$DISPLAY_NAME_FIELD;
	else
		$DisplayName=$LDAP_NAME_FIELD;

	$inquiry = "(&(objectCategory=person)$DIS_USERS_COND)";
	$Staff=$ldap->getArray($OU, $inquiry, array($DisplayName, $LDAP_MAIL_FIELD, $LDAP_INTERNAL_PHONE_FIELD, $LDAP_CITY_PHONE_FIELD, $LDAP_TITLE_FIELD, $LDAP_DEPARTMENT_FIELD, $LDAP_CELL_PHONE_FIELD, $LDAP_MANAGER_FIELD), array($LDAP_DEPARTMENT_FIELD, $DEP_SORT_ORDER, $LDAP_TITLE_FIELD, $STAFF_SORT_ORDER, $DisplayName));
	if(is_array($Staff))
		{
            $PrevDepartment="";
            $InclusionDep="";

            $SizeOf=sizeof($Staff[$DisplayName]);

            for($i=0; $i<$SizeOf; $i++)
            {

                if(!($PDF_HIDE_STAFF_WITHOUT_PHONES&&
                    (!isset($Staff[$LDAP_INTERNAL_PHONE_FIELD][$i]))&&
                    (!isset($Staff[$HIDE_CITY_PHONE_FIELD][$i]))&&
                    (!isset($Staff[$LDAP_CELL_PHONE_FIELD][$i]))
                )
                )
                {
                    $n=$i+1;

                    $FIO=explode(" ", $Staff[$DisplayName][$i]);
				$Surname=$Staff[$DisplayName][$i];
				$Name="";
				$Patronymic="";

				if(preg_match("/[ЁA-ZА-Я]{1}[ёa-zа-я-]+[\s]{1}[ЁA-ZА-Я]{1}[ёa-zа-я-]+[\s]{1}[ЁA-ZА-Я]{1}[ёa-zа-я-]+/u", $Staff[$DisplayName][$i]))
					{
					$Surname=$FIO[0];
					$Name=$FIO[1];
					$Patronymic=$FIO[2];
					}
				if(preg_match("/[ЁA-ZА-Я]{1}[ёa-zа-я-]+[\s]{1}[ЁA-ZА-Я]{1}[.]{1}[\s]{1}[ЁA-ZА-Я]{1}[ёa-zа-я-]+/u", $Staff[$DisplayName][$i]))
					{
					$Surname=$FIO[2];
					$Name=$FIO[0];
					$Patronymic=$FIO[1];
					}
                //$PrevDepartment="";
				$Department=$Staff[$LDAP_DEPARTMENT_FIELD][$i];
				$colspan=Staff::getNumStaffTableColls();

				if($PrevDepartment!=$Department)
					{
                        if(@strpos($Department, $InclusionDep)===0)
							$css="department";
						else
							{
							$css="division";
							$InclusionDep=$Department;
							}
					$html.="<tr><td colspan=\"".$colspan."\" class=\"department\"><div class=\"".$css."\">".Staff::makeDepartment($Department)."</div><img src=\"../skins/".$CURRENT_SKIN."/images/pdf/pixel_black.png\" vspace=\"1\" width=\"100%\" height=\"1px\"></td></tr>";
					$PrevDepartment=$Department;
					}
				else
					$html.="<tr><td colspan=\"".$colspan."\"><img src=\"../skins/".$CURRENT_SKIN."/images/pdf/divider.gif\" vspace=\"0\" width=\"100%\" height=\"1\"></td></tr>";
				$html.="<tr>
				<td class=\"name\"><span class=\"surname\">".$Surname."</span><br><span class=\"patronymic\">".$Name." ".$Patronymic."</span></td>";
				if(!$HIDE_CELL_PHONE_FIELD)
					$html.="<td class=\"cell_phone\">".Staff::makeCellPhone($Staff[$LDAP_CELL_PHONE_FIELD][$i], false)."</td>";

				if(!$HIDE_CITY_PHONE_FIELD)
					$html.="<td class=\"city_phone\">".Staff::makeCityPhone($Staff[$LDAP_CITY_PHONE_FIELD][$i], false)."</td>";

				$html.="
				<td class=\"internal_phone\">".Staff::makeInternalPhone($Staff[$LDAP_INTERNAL_PHONE_FIELD][$i], false)."</td>
				<td class=\"mail\">".$Staff[$LDAP_MAIL_FIELD][$i]."</td>
				<td class=\"position\">".Staff::makeTitle($Staff[$LDAP_TITLE_FIELD][$i])."</td>
				</tr>
				";
				}
			}
		}

	$html.="</table>";



try {

  $tempDir = '../temp/default/pdf';
  print ">>>>> dir1 = " . $tempDir;
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

$fileStyle1 = __DIR__ . "/../skins/".$CURRENT_SKIN."/css/pdf.css";
$fileStyle = "../skins/".$CURRENT_SKIN."/css/pdf.css";
print ">>>>> dir = " .  $fileStyle1;
          $stylesheet = file_get_contents($fileStyle1);

          $mpdf->WriteHTML($stylesheet, 1);
          $mpdf->WriteHTML($html,2);
          $mpdf->Output('pdf_departments.pdf', 'I');
          $mpdf->Output();

} catch (\Mpdf\MpdfException $e) {
    print "Creating an mPDF object failed with" . $e->getMessage();
}

/*
$mpdf->WriteHTML('<h1>Hello world!</h1>');
$mpdf->Output();

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output('pdf_departments.pdf', 'I');
$mpdf->Output();
*/

       // $mpdf=new mPDF(false, $PDF_LANDSCAPE?"A4-L":"A4", false, 'Arial', $PDF_MARGIN_LEFT, $PDF_MARGIN_RIGHT, $PDF_MARGIN_TOP, $PDF_MARGIN_BOTTOM);
//        $mpdf=new \Mpdf\Mpdf()(false, $PDF_LANDSCAPE?"A4-L":"A4", false, 'Arial', $PDF_MARGIN_LEFT, $PDF_MARGIN_RIGHT, $PDF_MARGIN_TOP, $PDF_MARGIN_BOTTOM);


        exit;

	}
?>