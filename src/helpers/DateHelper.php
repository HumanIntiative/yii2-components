<?php

namespace pkpudev\components\helpers;

class DateHelper
{
    public static function getHijriYear($year=null)
    {
        return ($year ?: date('Y')) - 579;
    }

    public static function getMonthNames($useZero=false)
    {
        if ($useZero) {
            $months = [
                '01'=>'Januari',    '02'=>'Februari',   '03'=>'Maret',
                '04'=>'April',      '05'=>'Mei',        '06'=>'Juni',
                '07'=>'Juli',       '08'=>'Agustus',    '09'=>'September',
                '10'=>'Oktober',    '11'=>'November',   '12'=>'Desember'
            ];
        } else {
            $months = [
                1=>'Januari',   2=>'Februari',  3=>'Maret',     4=>'April',
                5=>'Mei',       6=>'Juni',      7=>'Juli',      8=>'Agustus',
                9=>'September', 10=>'Oktober',  11=>'November', 12=>'Desember',
            ];
        }

        return $months;
    }

    public static function getMonthName($m)
    {
        $months = static::getMonthNames();
        return $months[$m];
    }

    public static function getDayNames()
    {
        return [
            0=>'Ahad',  1=>'Senin', 2=>'Selasa', 3=>'Rabu',
            4=>'Kamis', 5=>'Jumat', 6=>'Sabtu',
        ];
    }

    public static function getDayName($d)
    {
        $days = static::getDayNames();
        return $days[$d];
    }

    public static function getLocalFormat($data, $useDay=false)
    {
        $months = static::getMonthNames();
        $time   = strtotime($data);
        $nMonth = date('n', $time);
        $date   = date('j', $time);
        $month  = $months[$nMonth];
        $year   = date('Y', $time);

        $formatted = "$date $month $year";
        if ($useDay) {
            $days = static::getDayNames();
            $nDay   = date('w', $time);
            $day  = $days[$nDay];

            $formatted = "$day, $formatted";
        }
        return $formatted;
    }

    public static function dateSearch($column, $value)
    {
        $pattern = '/^(?:\s*(<>|<=|>=|<|>|=))?(.*)$/';

        if ($value != '' && preg_match($pattern, $value, $matches)) {
            return [$matches[1], "$column", date("Y-m-d", strtotime($matches[2]))];
        } else {
            return ["$column" => $value];
        }
    }
}