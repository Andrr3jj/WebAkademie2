<?php
$gift_cards_used = [];
if (empty($_SESSION['cart_gift_card'])) $_SESSION['cart_gift_card'] = [];
foreach ($_SESSION['cart_gift_card'] as $gift_card) { 
    $gift_card = json_decode($gift_card, true);
    $logData .= " gift_card: " . $gift_card . " x ";
    if ($gift_card['valid_until'] > time() and $gift_card['value'] > '0') {
        $gift_card_discount = $gift_card['value'];
        if ($price_total < $gift_card['value']) $gift_card_discount = $price_total;
        $price_total -= $gift_card_discount;
        $product_invoice = [
            "name" => $gift_card['code'],
            "qty" => 1,
            "price" => $gift_card_discount
        ];
        array_push($products_invoice, $product_invoice);
        
        $gift_card['gift_card_discount'] = $gift_card_discount;
        
        //$gift_card = json_encode($gift_card);
        $gift_cards_used[] = $gift_card;
    }
}
$fucker = '\\';
$gift_cards_used = json_encode($gift_cards_used);
$gift_cards_used = str_replace('\"', '\\\\"', $gift_cards_used);
