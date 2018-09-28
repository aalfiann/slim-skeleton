<?php 
namespace modules\core\helper;

    class EtagHelper {

        public static function updateByMinute($interval=1){
            $fix = date('Y-m-d H:');
            $rate = date('i');
            $maxminute = 60;
            $intervalminute = $interval;

            $n=0;
            for ($i = 0; $i <= $maxminute; $i+=$intervalminute) {
                if($i<=$rate) $n++;
            }
            return strtolower(md5($fix.$n.trim($_SERVER['REQUEST_URI'],'/')));
        }

        public static function updateByHour($interval=1){
            $fix = date('Y-m-d ');
            $rate = date('H');
            $maxhour = 24;
            $intervalhour = $interval;

            $n=0;
            for ($i = 0; $i <= $maxhour; $i+=$intervalhour) {
                if($i<=$rate) $n++;
            }
            return strtolower(md5($fix.$n.trim($_SERVER['REQUEST_URI'],'/')));
        }

        public static function updateByDay($interval=1){
            $fix = date('Y-m-');
            $rate = date('d');
            $maxday = date('t',strtotime(date('Y-m')));
            $intervalday = $interval;

            $n=0;
            for ($i = 0; $i <= $maxday; $i+=$intervalday) {
                if($i<=$rate) $n++;
            }
            return strtolower(md5($fix.$n.trim($_SERVER['REQUEST_URI'],'/')));
        }

        public static function updateByContent($content){
            if (is_array($content)){
                return strtolower(md5(json_encode($content)));
            }
            return strtolower(md5($content));
        }
    }