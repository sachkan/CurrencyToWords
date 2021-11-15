<?php
/**
 * Class CurrencyToWords
 * @author sac <sacchkkan@gmail.com>
 * @version 1.0.0
 */

class CurrencyToWords
{
    var $amount, $lang;

    public function __construct($lang)
    {
        $this->lang = $lang;
    }
    
    public function getWords()
    {

        $amount = $this->amount;

        ## Check data type
        if (gettype($amount) === "string") {
            $currency = $this->currency();
            $amount = floatval(str_ireplace($this->symbol, "", $this->amount));
        }

        ## fractions every 3 digits or less

        $number = number_format($amount,2,'.',',');
        $exp = explode(',', $number);
        $len = count($exp);
        $result = [];

        for ($i=0; $i < $len; $i++) {
            
            $arr[$i] = str_split($exp[$i]);

            if ($exp[$i]>99) {
                array_push( $result,
                    $this->digit($arr[$i][0], 3) ." ". implode(' ', $this->tens($arr[$i], intval($arr[$i][1].$arr[$i][2]), 2))
                );
            } else {
                array_push( $result,
                    implode(' ', $this->tens($arr[$i], intval($arr[$i][0].@$arr[$i][1]), 1))
                );
            }

            if ($len-1 != $i){
                switch ($len-$i) {
                    case 2:
                        $unit = $this->numeral(4);
                      break;
                    case 3:
                        $unit = $this->numeral(5);
                      break;
                    case 4:
                        $unit = $this->numeral(6);
                    break;
                    case 5:
                        $unit = $this->numeral(7);
                    break;
                    default:
                        $unit = $this->numeral(0);
                }
                array_push( $result, str_replace(' ','',$unit) );
            } else {
                ## fractions point
                array_push( $result, $this->punctuation(0) );
                $point = explode('.',$exp[$i])[1];
                array_push( $result,
                    implode(' ', $this->tens($point, intval($point), 1))
                );
            }

        }

        array_push( $result, $currency );

        return [
            // "array" => $result,
            "words" => implode(" ",array_filter($result)),
            "type" => gettype($this->amount),
            "amount" => $amount,
            "code" => @$currency
        ];
    }

    private function tens($arr, $digit, $len)
    {
        if ( $digit>19 ) {
            $result[] = $this->digit($arr[$len-1], 2);
            
            if($arr[$len])
                $result[] = $this->digit($arr[$len], 0);
        } else if ( $digit>10 && $digit<20 ) {  
            $result[] = $this->digit($arr[$len], 1);
        } else {
            $result[] = $this->digit($digit, 0);
        }

        return $result;
    }
    
    public function currency()
    {
        ## add currency code
        $code = ["Rp"=>'IDR', "$"=>'USD'];

        $this->symbol = str_replace( array_merge(range(0,9), [".",","]), "", $this->amount);

        ## auto change currency
        switch ($this->symbol) {
            case "Rp":
                $this->lang = "id";
              break;
            case "$":
                $this->lang = "us";
              break;
            default:
                $this->lang = "";
        }

        return !empty($code[$this->symbol]) ? $code[$this->symbol] : "";
    }

    private function punctuation($idx)
    {
        ## add punctuation
        $punctuation = [
            "id" => ["Titik", "Koma"],
            "us" => ["Point", "Comma"]
        ];

        return $punctuation[$this->lang][$idx];
    }

    private function numeral($idx)
    {
        ## add & set the numeral
        $num = [
            "id" => ["", " Belas", " Puluh", " Ratus", " Ribu", " Juta", " Milyar", " Triliun", " Kuadriliun"],
            "us" => ["", "teen", "ty", " Hundred", " Thousand", " Million", " billion", " Trillion", " Quadrillion"]
        ];

        return $num[$this->lang][$idx];
    }

    private function digit($idx, $num)
    {
        ## add number
        $digit = [
            "id" => ["Nol", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh"],
            "us" => ["Zero", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten"]
        ];

        if ($this->lang =='id' && $idx ==1 && $num)
            return 'Se'.lcfirst(str_replace(' ','',$this->numeral($num)));
        else if ($this->lang =='us')
        
            ## set & replace words USD
            return str_replace([
                "Oneteen",
                "Twoteen",
                "Threeteen",
                "Fiveteen",
                "Eightteen",
    
                "Twoty",
                "Threety",
                "Fourty",
                "Fivety",
                "Eightty"            
            ],[
                "Eleven",
                "Twelve",
                "Thirteen",
                "Fifteen",
                "Eighteen",
    
                "Twenty",
                "Thirty",
                "Forty",
                "Fifty",
                "Eighty"
            ],  $digit[$this->lang][$idx].$this->numeral($num) );
        else
            return $digit[$this->lang][$idx].$this->numeral($num);

    }
    
}

?>
