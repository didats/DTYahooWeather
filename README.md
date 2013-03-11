# DTYahooWeather

DTYahooWeather is a PHP class to get the weather condition based on your country or city name. It uses Yahoo Weather APIs, and you no longer worry about getting the WOEID anymore, because it's already there.

## Contact
[Blog site](http://didats.net)
[@didats](https://twitter.com/didats)

## Usage

```php
<?php
	require "DTYahooWeather.php"
	$weather = new Weather($yourCityName);

	// get woeid. 
	// It will be better if you save the woeid somewhere so the class doesn't need to grab it again
	$woeid = $weather->woeid();

	// if you already have the woeid, 
	// set it here
	$weather->woeid = $woeid;

	// get the weather data as an array
	$weather_data = $weather->get();
?>
```
## Result Data

```text
Array ( [cityName] => Kuwait [regionName] => [countryName] => Kuwait [windChill] => 77 [windDirection] => 350 [windSpeed] => 21 [humidity] => 13 [visibility] => 6.21 [pressure] => 29.94 [rising] => 2 [sunrise] => 6:01 am [sunset] => 5:52 pm [latitude] => 29.31 [longitude] => 47.49 [weatherLink] => http://us.rd.yahoo.com/dailynews/rss/weather/Kuwait__KW/*http://weather.yahoo.com/forecast/KUXX0003_f.html [weatherCode] => 34 [weatherName] => Fair [weatherTemperatureCelcius] => 24.44 [weatherTemperatureFahrenheit] => 76 ) 
```

## License

This code is distributed under the terms and conditions of the MIT license.

Copyright (c) 2013 Didats Triadi

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.