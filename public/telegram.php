<?php

$token = '5529634291:AAHKLV_UODdHTdSFjIiKFbNXYIU8qy1Cx1U';


$getUpdatesUri = sprintf('https://api.telegram.org/bot%s/getUpdates', $token);
$sendMessageUri = sprintf('https://api.telegram.org/bot%s/sendMessage', $token);

$requestParameters = [
    'offset' => null
];


while (true) {
    $updates = json_decode(file_get_contents($getUpdatesUri . '?' . http_build_query(
            $requestParameters
        )), true);


    foreach ($updates['result'] as $update) {

        $currency = json_decode(file_get_contents('https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5'), true);

        $name = explode(' ', $update['message']['text']); // конвертируемое значение

        $amount = $name[0];

        $ccy = $name[1];

        $baseCcy = 'UAH';



        $bet = null;
        foreach ($currency as $data) {
            if ($data['ccy'] === $ccy & $data['base_ccy'] === $baseCcy) {
                $bet = $data;

            } else {

             throw new Exception('i don\'t know rate ' . $update['message']['text']);

            }
        }


        $responseParameters = [
            'chat_id' => $update['message']['chat']['id'],
            'text' => $amount . ' ' . $ccy . ' = ' . $amount * $bet['sale'] . ' ' . $baseCcy,
        ];

        file_get_contents($sendMessageUri . '?' . http_build_query($responseParameters));

        $requestParameters['offset'] = $update['update_id'] + 1;

        echo $requestParameters['offset'];

    }
}
