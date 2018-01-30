<?php

use ru860e\rest\Application;
use ru860e\rest\LDAP;
use ru860e\rest\Staff;

/** Error reporting */
/*
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
*/




require_once('../config.php');
require_once("../libs/forms.php");
require_once("../libs/staff.php");
require_once("../libs/phones.php");
require_once("../libs/pdf.php");

$date_today = date("m.d.y");
$fileNnameEXEL = '"' . $date_today . '-tel.xlsx"';

//----------------------  EXEL ------------------------------------
if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');

/** Подключаем PHPExcel */
require_once dirname(__FILE__) . '/../libs/PHPExcel/PHPExcel.php';


// Создаем новый PHPExcel object
$objPHPExcel = new PHPExcel();

// Устанавливаем индекс активного листа
$objPHPExcel->setActiveSheetIndex(0);
// Получаем активный лист

$objSheetPHPExcel = $objPHPExcel->getActiveSheet();

// Ширина столбца                               
$objSheetPHPExcel->getColumnDimension('A')->setWidth(45);
$objSheetPHPExcel->getColumnDimension('B')->setWidth(24);
$objSheetPHPExcel->getColumnDimension('C')->setWidth(10);
$objSheetPHPExcel->getColumnDimension('D')->setWidth(17);
$objSheetPHPExcel->getColumnDimension('E')->setWidth(58);
$objSheetPHPExcel->getColumnDimension('F')->setWidth(17);
$objSheetPHPExcel->getColumnDimension('G')->setWidth(30);


//$objSheetPHPExcel->getColumnDimension('A')->set;

$ii = 1; //Строки в EXEL
// Подписываем лист
$objSheetPHPExcel->setTitle('По отделам');

//Шапка
$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A' . $ii, 'Отдел')
        ->setCellValue('B' . $ii, 'ФИО')
        ->setCellValue('C' . $ii, 'Телефон')
        ->setCellValue('D' . $ii, 'Мобильный')
        ->setCellValue('E' . $ii, 'Должность')
        ->setCellValue('F' . $ii, 'Комната')
        ->setCellValue('G' . $ii, 'Email');


$ii++;

//Стиль шапки
$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB('EEEEEE');
$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);


// По центру
$objSheetPHPExcel->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//Высота Строки
$objSheetPHPExcel->getRowDimension('1')->setRowHeight(30);

//-------------------------------------------------------------




if ($ENABLE_EXEL_EXPORT) {
    Application::makeLdapConfigAttrLowercase();
    $menu_marker = "si_export_pdf_department";
    @$BOOKMARK_NAME = ($_POST['bookmark_name']) ? $_POST['bookmark_name'] : (($_GET['bookmark_name']) ? $_GET['bookmark_name'] : current(array_keys($BOOKMARK_NAMES[current(array_keys($BOOKMARK_NAMES))])) );
    @$bookmark_attr = ($_POST['bookmark_attr']) ? $_POST['bookmark_attr'] : (($_GET['bookmark_attr']) ? $_GET['bookmark_attr'] : current(array_keys($BOOKMARK_NAMES)));
    $html ='';
    $html.=PDF::get_pdf_head();

    $html.="
	<table cellpadding='0' border='0' cellspacing='0' class='staff'>
	";

    $ldap = new LDAP($LDAPServer, $LDAPUser, $LDAPPassword);
    $CompanyNameLdapFilter = Application::getCompanyNameLdapFilter();

    if ($USE_DISPLAY_NAME)
        $DisplayName = $DISPLAY_NAME_FIELD;
    else
        $DisplayName = $LDAP_NAME_FIELD;


    $request = "(&(objectCategory=person)$DIS_USERS_COND)";
    $Staff = $ldap->getArray($OU, $request, array($DisplayName, $LDAP_MAIL_FIELD, $LDAP_INTERNAL_PHONE_FIELD, $LDAP_CITY_PHONE_FIELD, $LDAP_TITLE_FIELD, $LDAP_DEPARTMENT_FIELD, $LDAP_CELL_PHONE_FIELD, $LDAP_MANAGER_FIELD, $LDAP_ROOM_NUMBER_FIELD), array($LDAP_DEPARTMENT_FIELD, $DEP_SORT_ORDER, $LDAP_TITLE_FIELD, $STAFF_SORT_ORDER, $DisplayName));
    if (is_array($Staff)) {
        $SizeOf = sizeof($Staff[$DisplayName]);

        for ($i = 0; $i < $SizeOf; $i++) {
            if (!($PDF_HIDE_STAFF_WITHOUT_PHONES &&
                (!isset($Staff[$LDAP_INTERNAL_PHONE_FIELD][$i])) &&
                (!isset($Staff[$HIDE_CITY_PHONE_FIELD][$i])) &&
                (!isset($Staff[$LDAP_CELL_PHONE_FIELD][$i]))))
            {
                $n = $i + 1;

                $FIO = explode(" ", $Staff[$DisplayName][$i]);

                $Surname = $Staff[$DisplayName][$i];
                $Name = "";
                $Patronymic = "";

                if (preg_match("/[ЁA-ZА-Я]{1}[ёa-zа-я-]+[\s]{1}[ЁA-ZА-Я]{1}[ёa-zа-я-]+[\s]{1}[ЁA-ZА-Я]{1}[ёa-zа-я-]+/u", $Staff[$DisplayName][$i])) {
                    $Surname = $FIO[0];
                    $Name = $FIO[1];
                    $Patronymic = $FIO[2];
                }
                if (preg_match("/[ЁA-ZА-Я]{1}[ёa-zа-я-]+[\s]{1}[ЁA-ZА-Я]{1}[.]{1}[\s]{1}[ЁA-ZА-Я]{1}[ёa-zа-я-]+/u", $Staff[$DisplayName][$i])) {
                    $Surname = $FIO[2];
                    $Name = $FIO[0];
                    $Patronymic = $FIO[1];
                }

                $Department = $Staff[$LDAP_DEPARTMENT_FIELD][$i];
                $colspan = Staff::getNumStaffTableColls();

                $Departam = Staff::makeDepartment($Department);
                if ($Departam == '') {
                    continue;
                }

                if (!$HIDE_CELL_PHONE_FIELD)
                    $cellPhone = Staff::makeCellPhone($Staff[$LDAP_CELL_PHONE_FIELD][$i], false);
                // Городской телефон
                if (!$HIDE_CITY_PHONE_FIELD)
                    $cityPhone = Staff::makeCityPhone($Staff[$LDAP_CITY_PHONE_FIELD][$i], false);


                $internapPhone = $Staff[$LDAP_INTERNAL_PHONE_FIELD][$i];
                $post = Staff::makeTitle($Staff[$LDAP_TITLE_FIELD][$i]);

                $cellPhone = $Staff[$LDAP_CELL_PHONE_FIELD][$i];

                $roomNuber = $Staff[$GLOBALS['LDAP_ROOM_NUMBER_FIELD']][$i];
                $mail = $Staff[$LDAP_MAIL_FIELD][$i];

                $html.=
                        $i .
                        $Departam . // Отдел
                        $Surname . // Фамилия
                        " " . $Name . // Имя
                        // "<<< ".$Patronymic.">>>".
                        // Внутренний телефон
                        $internapPhone .
                        // Мобильный телефон
                        $cellPhone .
                        // Должность
                        $post .
                        // Комната
                        $roomNuber .
                        // Почта
                        $mail;


                // Граница
                $objPHPExcel->getActiveSheet()->getStyle("A$ii:G$ii")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
                // Текстовый
                $objPHPExcel->getActiveSheet()->getStyle("A$ii:G$ii")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $ii, $Department)
                        ->setCellValue('B' . $ii, $Surname)
                        ->setCellValue('C' . $ii, $internapPhone)
                        ->setCellValue('D' . $ii, $cellPhone)
                        ->setCellValue('E' . $ii, $post)
                        ->setCellValue('F' . $ii, $roomNuber)
                        ->setCellValue('G' . $ii, $mail);
                

                $ii++;


            }
        }
    }


}



$objPHPExcel->setActiveSheetIndex(0);



// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $fileNnameEXEL);
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>