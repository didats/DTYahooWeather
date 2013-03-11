<?php
	class Weather {
		var $cityName;
		var $countryName;
		var $regionName;
		var $woeid;
		
		var $windChill;
		var $windDirection;
		var $windSpeed;
		
		var $humidity;
		var $visibility;
		var $pressure;
		var $rising;
		
		var $sunrise;
		var $sunset;
		
		var $latitude;
		var $longitude;
		
		var $weatherLink;
		var $weatherCode;
		var $weatherName;
		var $weatherTemperature;
		
		/*
		Method to get the content of URL
		*/
		private function get_content($url) {
			$curl = curl_init();
			curl_setopt_array($curl, array(
									CURLOPT_RETURNTRANSFER => 1,
									CURLOPT_URL => $url,
									CURLOPT_SSL_VERIFYPEER => 0,
									CURLOPT_USERAGENT => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:19.0) Gecko/20100101 Firefox/19.0"
								));
			$content = curl_exec($curl);
			curl_close($curl);
			
			return $content;
		}
		
		private function xml2array(&$string) {
		    $parser = xml_parser_create();
		    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		    xml_parse_into_struct($parser, $string, $vals, $index);
		    xml_parser_free($parser);
		
		    $mnary=array();
		    $ary=&$mnary;
		    foreach ($vals as $r) {
		        $t=$r['tag'];
		        if ($r['type']=='open') {
		            if (isset($ary[$t])) {
		                if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
		                $cv=&$ary[$t][count($ary[$t])-1];
		            } else $cv=&$ary[$t];
		            if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
		            $cv['_c']=array();
		            $cv['_c']['_p']=&$ary;
		            $ary=&$cv['_c'];
		
		        } elseif ($r['type']=='complete') {
		            if (isset($ary[$t])) { // same as open
		                if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
		                $cv=&$ary[$t][count($ary[$t])-1];
		            } else $cv=&$ary[$t];
		            if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
		            $cv['_v']=(isset($r['value']) ? $r['value'] : '');
		
		        } elseif ($r['type']=='close') {
		            $ary=&$ary['_p'];
		        }
		    }    
		    
		    $this->_del_p($mnary);
		    return $mnary;
		}
		
		// _Internal: Remove recursion in result array
		private function _del_p(&$ary) {
		    foreach ($ary as $k=>$v) {
		        if ($k==='_p') unset($ary[$k]);
		        elseif (is_array($ary[$k])) $this->_del_p($ary[$k]);
		    }
		}
		
		// Array to XML
		private function ary2xml($cary, $d=0, $forcetag='') {
		    $res=array();
		    foreach ($cary as $tag=>$r) {
		        if (isset($r[0])) {
		            $res[]=$this->ary2xml($r, $d, $tag);
		        } else {
		            if ($forcetag) $tag=$forcetag;
		            $sp=str_repeat("\t", $d);
		            $res[]="$sp<$tag";
		            if (isset($r['_a'])) {foreach ($r['_a'] as $at=>$av) $res[]=" $at=\"$av\"";}
		            $res[]=">".((isset($r['_c'])) ? "\n" : '');
		            if (isset($r['_c'])) $res[]=$this->ary2xml($r['_c'], $d+1);
		            elseif (isset($r['_v'])) $res[]=$r['_v'];
		            $res[]=(isset($r['_c']) ? $sp : '')."</$tag>\n";
		        }
		        
		    }
		    return implode('', $res);
		}
		
		// Insert element into array
		private function ins2ary(&$ary, $element, $pos) {
		    $ar1=array_slice($ary, 0, $pos); $ar1[]=$element;
		    $ary=array_merge($ar1, array_slice($ary, $pos));
		}
		
		public function Weather($name) {
			$this->cityName = $name;
		}
		
		// get the woeid to use with yahoo weather api
		public function woeid() {
			
			$this->url = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20geo.places%20where%20text%3D%22".$this->cityName."%22&format=json&callback=";
			
			$json = json_decode($this->get_content($this->url));
			
			$count = $json->query->count;
			$places = "";
			
			if($count > 1) {
				$places = $json->query->results->place[0];
			}
			else {
				$places = $json->query->results->place;
			}
			
			$this->woeid = $places->woeid;
			return $places->woeid;
		}
		
		// get the weather
		public function get() {
			$this->url = "http://weather.yahooapis.com/forecastrss?w=".$this->woeid;
			
			$content = $this->get_content($this->url);
			$arrdata = $this->xml2array($content);
			
			$channel = $arrdata['rss']['_c']['channel'];
			
			// location
			$location = $channel['_c']['yweather:location']['_a'];
			$this->cityName = $location['city'];
			$this->regionName = $location['region'];
			$this->countryName = $location['country'];
			
			// wind
			$wind = $channel['_c']['yweather:wind']['_a'];
			$this->windChill = $wind['chill'];
			$this->windDirection = $wind['direction'];
			$this->windSpeed = $wind['speed'];
			
			// atmosphere
			$atmosphere = $channel['_c']['yweather:atmosphere']['_a'];
			$this->humidity = $atmosphere['humidity'];
			$this->visibility = $atmosphere['visibility'];
			$this->pressure = $atmosphere['pressure'];
			$this->rising = $atmosphere['rising'];
			
			// astronomy
			$astronomy = $channel['_c']['yweather:astronomy']['_a'];
			$this->sunrise = $astronomy['sunrise'];
			$this->sunset = $astronomy['sunset'];
			
			// position
			$item = $channel['_c']['item']['_c'];
			$this->latitude = $item['geo:lat']['_v'];
			$this->longitude = $item['geo:long']['_v'];
			
			// condition
			$this->weatherLink = $item['link']['_v'];
			$this->weatherCode = $item['yweather:condition']['_a']['code'];
			$this->weatherName = $item['yweather:condition']['_a']['text'];
			$this->weatherTemperature = $item['yweather:condition']['_a']['text'];
			
			return array(
				'cityName' => $this->cityName,
				'regionName' => $this->regionName,
				'countryName' => $this->countryName,
				'windChill' => $this->windChill,
				'windDirection' => $this->windDirection,
				'windSpeed' => $this->windSpeed,
				'humidity' => $this->humidity,
				'visibility' => $this->visibility,
				'pressure' => $this->pressure,
				'rising' => $this->rising,
				'sunrise' => $this->sunrise,
				'sunset' => $this->sunset,
				'latitude' => $this->latitude,
				'longitude' => $this->longitude,
				'weatherLink' => $this->weatherLink,
				'weatherCode' => $this->weatherCode,
				'weatherName' => $this->weatherName,
				'weatherTemperature' => ($this->weatherTemperature - 32) / 1.8
			);
		}
		
	}