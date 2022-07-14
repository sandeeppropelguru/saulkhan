<?php


echo "heloo";
$data=file_get_contents('php://input');
$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
$txt = json_decode($data, true);;
$type=$txt['data']['type'];
$justcall_number=$txt['data']['justcall_number'];
$contact_name=$txt['data']['contact_name'];
$contact_number=$txt['data']['contact_number'];
if(!empty($txt['data']['contact_email'])){
    $contact_email=$txt['data']['contact_email'];
}else{
    $contact_email="";
}

$content=$txt['data']['content'];
$agent_name=$txt['data']['agent_name'];



//Genrate Access token


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://accounts.zoho.com.au/oauth/v2/token",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"client_id\"\r\n\r\n1000.OSMHSZ82Q0Q6P89WLG6PQFJC7ZZ7CG\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"client_secret\"\r\n\r\n2a92c55eee30cb4b6b8e57a4a2b6dc5e38f7e54567\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"scope\"\r\n\r\nDesk.tickets.ALL,Desk.contacts.READ,Desk.basic.READ,Desk.search.READ,Desk.contacts.READ,Desk.settings.READ,Desk.tasks.ALL,ZohoCRM.modules.ALL,Desk.contacts.UPDATE,ZohoCreator.report.READ,ZohoCreator.form.CREATE,ZohoCreator.report.UPDATE,ZohoCreator.report.CREATE,Desk.tasks.WRITE,Desk.tasks.CREATE,ZohoBooks.fullaccess.all\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"redirect_uri\"\r\n\r\nhttps://3cd1-180-151-16-213.ngrok.io/auth-redirect\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"grant_type\"\r\n\r\nrefresh_token\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"refresh_token\"\r\n\r\n1000.a91ea1b7a2839ae34505138981244961.fdfee87b5db2282e6714c290124e1445\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"Content-Type\"\r\n\r\napplication/x-www-form-urlencoded\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
    "postman-token: 8a571adf-67fb-4199-38e1-3e6aaa10bc5c"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
    $deoced_res2 = json_decode($response, true);
}
if (!empty($deoced_res2['access_token'])) {

    $accessToken=$deoced_res2['access_token'];

    // Check that ticket is exits or not

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://desk.zoho.com.au/api/v1/tickets/search?limit=100&phone=".$contact_number,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "authorization: Zoho-oauthtoken ".$accessToken,
        "cache-control: no-cache",
        "charset: UTF-8",
        "content-type: application/json",
        "postman-token: 6a528e79-dd31-76b9-6080-a56ebb52dce0"
    ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
    echo "cURL Error #:" . $err;
    } else {
        $ticketdata=json_decode($response, true);
        
        $tcket_id=$ticketdata['data'][0]['id'];
        
        if($ticketdata['count']>0){

            

            

            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://desk.zoho.com.au/api/v1/tasks",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\r\n  \"subject\" : \"Incomming SMS $contact_name\",\r\n  \"departmentId\" : \"10264000000012806\",\r\n  \"ticketId\":\"$tcket_id\",\r\n  \"description\":\"$content\"\r\n}",
            CURLOPT_HTTPHEADER => array(
                "authorization: Zoho-oauthtoken ".$accessToken,
                "cache-control: no-cache",
                "charset: UTF-8",
                "content-type: application/json",
                "postman-token: 752fe70f-973f-af69-7e45-0feeba239490"
            ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
            echo "cURL Error #:" . $err;
            } else {
            echo $response;
            fwrite($myfile, $tcket_id);
            }
        }else{
            

            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://desk.zoho.com.au/api/v1/tickets",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\r\n\t\r\n  \"subject\" : \"$contact_name\",\r\n  \"departmentId\" : \"10264000000012806\",\r\n \"contact\":{\r\n \t\"lastName\":\"$contact_name\",\r\n \t\"phone\":\"$contact_number\"\r\n \t\r\n \t},\r\n  \"description\" : \"$content\",\r\n  \r\n \r\n  \"phone\" : \"$contact_number\",\r\n  \"email\" : \"$contact_email\",\r\n  \"status\" : \"Open\"\r\n}",
            CURLOPT_HTTPHEADER => array(
                "authorization: Zoho-oauthtoken ".$accessToken,
                "cache-control: no-cache",
                "charset: UTF-8",
                "content-type: application/json",
                "postman-token: 1e20015e-15f3-ae76-b739-5cda9a69b15b"
            ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
            echo "cURL Error #:" . $err;
            } else {
            echo $response;
            fwrite($myfile, $response);
            fclose($myfile);
            }
        }

    }
}
