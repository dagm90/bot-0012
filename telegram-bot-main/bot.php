<?php
    date_default_timezone_set("africa/ethiopia");
    //Data From Webhook
    $content = file_get_contents("php://input");
    $update = json_decode($content, true);
    $chat_id = $update["message"]["chat"]["id"];
    $message = $update["message"]["text"];
    $id = $update["message"]["from"]["id"];
    $username = $update["message"]["from"]["username"];
    $firstname = $update["message"]["from"]["first_name"];
    $bot_name = "" ;//your bot name
 /// for broadcasting in Channel
$channel_id = "-1001351780832";

    //Extact match Commands
    if($message == "/start"){
        send_message($chat_id, "ሰላም $firstname እኔ $bot_name \nSupport Group - @gullet_shopping \nUse /cmds to view commands \nBot developed by Dagi ");
    }

    if($message == "/cmds"){
        send_message($chat_id, "
          /search <input> (Google search)
          \n/dict <word> (Dicitonary)
          \n/weather <name of your city> (Current weather Status)
          \n/date (today's date)
          \n/time (current time)
          \n/info (User Info)
          ");
    }

    if($message == "/dice"){
        sendDice($chat_id, "🎲");
    }
    if($message == "/date"){
        $date = date("d/m/y");
        send_message($chat_id, $date);
    }
   if($message == "/time"){
        $time = date("h:i a", time());
        send_message($chat_id, $time);
    }

     if($message == "/info"){
        send_message($chat_id, "User Info \nName: $firstname\nID:$id \nUsername: @$username");
    }

if($message == "/help"){
        send_message($chat_id, "Contact @gullet_shopping_seller");
    }

///Commands with text


    //Google Search
if (strpos($message, "/search") === 0) {
        $search = substr($message, 8);
         $search = preg_replace('/\s+/', '+', $search);
    if ($search != null) {
     send_message($chat_id, "https://www.google.com/search?q=".$search);
    }
  }


///Channel BroadCast
if (strpos($message, "/broadcast") === 0) {
$broadcast = substr($message, 11);
// id == (admins user id)
if ($id ==  ) {
  send_Cmessage($channel_id, $broadcast);
}
}


    //Wheather API
if(strpos($message, "/weather") === 0){
        $location = substr($message, 9);
   $curl = curl_init();
   curl_setopt_array($curl, [
CURLOPT_URL => "http://api.openweathermap.org/data/2.5/weather?q=$location&appid=89ef8a05b6c964f4cab9e2f97f696c81",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 50,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => [
		"Accept: */*",
        "Accept-Language: en-GB,en-US;q=0.9,en;q=0.8,hi;q=0.7",
        "Host: api.openweathermap.org",
        "sec-fetch-dest: empty",
		"sec-fetch-site: same-site"
  ],
]);


$content = curl_exec($curl);
curl_close($curl);
$resp = json_decode($content, true);

$weather = $resp['weather'][0]['main'];
$description = $resp['weather'][0]['description'];
$temp = $resp['main']['temp'];
$humidity = $resp['main']['humidity'];
$feels_like = $resp['main']['feels_like'];
$country = $resp['sys']['country'];
$name = $resp['name'];
$kelvin = 273;
$celcius = $temp - $kelvin;
$feels = $feels_like - $kelvin;

if ($location = $name) {
        send_message($chat_id, "
    Weather at $location: $weather
Status: $description
Temp : $celcius °C
Feels Like : $feels °C
Humidity: $humidity
Country: $country
Checked By @$username ");
}
else {
           send_message($chat_id, "Invalid City");
}
    }


///Dicitonary API
if(strpos($message, "/dict") === 0){
        $dict = substr($message, 6);
   $curl = curl_init();
   curl_setopt_array($curl, [
	CURLOPT_URL => "https://api.dictionaryapi.dev/api/v2/entries/en/$dict",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => [
		"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
        "Accept-Language: en-GB,en-US;q=0.9,en;q=0.8,hi;q=0.7",
        "Host: oxforddictionaryapi.herokuapp.com",
        "Sec-Fetch-Dest: empty",
        "Sec-Fetch-Mode: cors",
        "Sec-Fetch-Site: cross-site",
        ],
]);


  $dictionary = curl_exec($curl);
  curl_close($curl);

$out = json_decode($dictionary, true);
$word = $out[0]['word'];
$noun= $out[0]['meaning']['noun'][0]['definition'];
$verb = $out[0]['meaning']['verb'][0]['definition'];
$adjective = $out[0]['meaning']['adjective'][0]['definition'];
$adverb = $out[0]['meaning']['adverb'][0]['definition'];	
$pronoun = $out[0]['meaning']['pronoun'][0]['definition'];

if ($word = $dict) {
        send_message($chat_id, "
Word: $word
Noun : $noun
Pronoun: $pronoun
Verb : $verb
Adjective: $adjective
Adverb: $adverb
Checked By @$username ");
    }
    else {
        send_message($chat_id, "Invalid Input");
    }
}


     ///Send Message (Global)
    function send_message($chat_id, $message){
        $apiToken =  "API_TOKEN";
        $text = urlencode($message);
        file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?chat_id=$chat_id&text=$text");
    }

//Send Messages with Markdown (Global)
      function send_MDmessage($chat_id, $message){
       $apiToken =  "API_TOKEN";
        $text = urlencode($message);
        file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?chat_id=$chat_id&text=$text&parse_mode=Markdown");
    }


///Send Message to Channel
      function send_Cmessage($channel_id, $message){
       $apiToken =  "API_TOKEN";
        $text = urlencode($message);
        file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?chat_id=$channel_id&text=$text");
    }

 function sendDice($chat_id, $message){
       $apiToken =  "API_TOKEN";
        file_get_contents("https://api.telegram.org/bot$apiToken/sendDice?chat_id=$chat_id&emoji=$message");
    }
?>
