<?php
namespace ru860e\rest;

class Localization
	{
	private $localeVars;


	function __construct($YmlFile)
		{
		$this->localeVars=Spyc::YAMLLoad($YmlFile);
		}


	public function translit($str) 
		{
    	$rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
    	$lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
    	return str_replace($rus, $lat, $str);
  		}

    /**
     * @param $Parameter
     * @return mixed
     */
	public function get($parameter)
		{
		return $this->localeVars[$parameter];
		}

	public function getArr($parameter)
		{
		return $this->localeVars[$parameter];
		}
    /**
     * Возвращает массив значений "Locale"
     *
     * @return array
     */
    public function getLocaleVars()
    {
        return $this->localeVars;
    }


	public function ending($number, $singular, $plural, $zero_plural)
		{
		$LastDigit=substr($number, -1, 1);
		if($LastDigit==2 || $LastDigit==3 || $LastDigit==4)
			return $number." ".$plural;
		if($LastDigit==0 || $LastDigit==5 || $LastDigit==6 || $LastDigit==7 || $LastDigit==8 || $LastDigit==9)
			return  $number." ".$zero_plural;
		if($LastDigit==1)
			return  $number." ".$singular;
		}

	}
?>