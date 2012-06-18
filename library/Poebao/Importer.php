<?php

class Poebao_Importer 
{

    public function init()
    {

    }

    public function figureOutExtension($filename, $path = null)
    {
        $extensions = array('jpg', 'jpeg', 'gif', 'png');
        $filebase = $path . $filename;

        foreach ($extensions as $key => $value) {

            $file = $filebase . '.' . $value;
            if (file_exists($file)) {
                return $file;
            }
        }
        return false;
    }

    public function getRandomAuthor()
    {
        $data = array(
            'koni', 'frentz', 'Morek', 'Mochal', 'Ktokolwiek', 'Kgik', 'KtoCzytaTenWie', 'Kartki', 'Kart', 'KenAdams', 'K2techOo', 'Kartissimo',
            'KotLolek', 'Kasztelan81', 'Koleszko', 'killMEnow', 'kitkit', 'KOBRA', 'Kinofon', 'KornikNewAge', 'kratka8910', 'Jacek', 'Koba',
            'HansKlos', 'Wypier', 'Piotr', 'Tomasz', 'Tomeczko', 'Lozeczko', 'Basia'
            );

        return $data[rand(0, sizeof($data) - 1)];
    }

    public function getRandomSource()
    {
        $data = array(
            'http://www.interia.pl', 'http://www.rmf.fm', 'http://www.rmf.pl', 'http://www.rmf24.pl', 'http://sport.interia.pl', 'http://facet.interia.pl',
            'http://kobieta.interia.pl', 'http://www.9gag.com', 'http://www.tumblr.com', 'http://www.internet.com', 'http://www.foobar.com'
            );

        return $data[rand(0, sizeof($data) - 1)];
    }

    public function getRandomDate($startDate, $endDate)
    {
        $days = round((strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24));
        $n = rand(0, $days);
        return date("Y-m-d H:i:s", strtotime("$startDate + $n days") + rand(0, 60 * 60 * 24));    
    }
}