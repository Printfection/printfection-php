<?php

require_once('./lib/Printfection.php');

Printfection::setApiKey('13403fa234d5239e6c005b626146cacb5f35e50b');

echo("\n==============[ CAMPAIGNS ]==============\n");

echo("\n--------------( List )--------------\n");
$campaigns = Printfection_Campaign::all(array('limit'=>1));
echo($campaigns . "\n");

echo("\n--------------( Pagination )--------------\n");
$next_campaign = $campaigns->next();
echo($next_campaign);

echo("\n--------------( One )--------------\n");
$campaign = Printfection_Campaign::retrieve(26);
echo($campaign);

echo("\n--------------( Nonexistent )--------------\n");
try {
	$campaign = Printfection_Campaign::retrieve(999999999);
	echo($campaign);
} catch (Exception $e) {
	echo($e->getMessage());
}

echo("\n==============[ Items ]==============\n");

echo("\n--------------( List )--------------\n");
$items = Printfection_Item::all(array('limit'=>1));
echo($items . "\n");

echo("\n--------------( Pagination )--------------\n");
$next_item = $items->next();
echo($next_item);

echo("\n--------------( One )--------------\n");
$item = Printfection_Item::retrieve(82);
echo($item);

echo("\n--------------( Nonexistent )--------------\n");
try {
	$item = Printfection_Item::retrieve(999999999);
	echo($item);
} catch ( Exception $e ) {
	echo($e->getMessage());
}

echo("\n==============[ ORDERS ]==============\n");
$orders = Printfection_Order::all(array('limit'=>1));
echo($orders);

echo("\n--------------( Pagination )--------------\n");
$next_order = $orders->next();
echo($next_order);

echo("\n--------------( One )--------------\n");
$order = Printfection_Order::retrieve(1131);
echo($order);

echo("\n--------------( Nonexistent )--------------\n");
try {
	$order = Printfection_Order::retrieve(999999999);
	echo($order);
} catch ( Exception $e ) {
	echo($e->getMessage());
}

echo("\n--------------( Create [Campaign ID Only] )--------------\n");
$order = Printfection_Order::create(array(
		'campaign_id' => 26
	));
echo($order);

echo("\n--------------( Place [No Shipping + Items] )--------------\n");
try {
	$place = $order->place();
	echo($place);
} catch (Exception $e) {
	echo($e->getMessage());
}

echo("\n--------------( Create [Campaign ID + Shipping] )--------------\n");
$order = Printfection_Order::create(array(
		'campaign_id' => 26,
		'ship_to' => array(
				'name' => 'Zachary Flower',
				'address' => '2262 Bristol Street',
				'city' => 'Superior',
				'state' => 'Colorado',
				'zip' => '80027',
				'country' => 'US',
				'email' => 'zach@ninjaninja.net'
			)
	));
echo($order);

echo("\n--------------( Place [No Items] )--------------\n");
try {
	$place = $order->place();
	echo($place);
} catch (Exception $e) {
	echo($e->getMessage());
}

echo("\n--------------( Create [Campaign ID + Shipping + 1 Item] )--------------\n");
$order = Printfection_Order::create(array(
		'campaign_id' => 26,
		'ship_to' => array(
				'name' => 'Zachary Flower',
				'address' => '2262 Bristol Street',
				'city' => 'Superior',
				'state' => 'Colorado',
				'zip' => '80027',
				'country' => 'US',
				'email' => 'zach@ninjaninja.net'
			),
		'lineitems' => array(
				array(
						'item_id' => 76,
						'size_id' => 4921,
						'quantity' => rand(1,100)
					)
			)
	));
echo($order);

echo("\n--------------( Place )--------------\n");
try {
	$place = $order->place();
	echo($place);
} catch (Exception $e) {
	echo($e->getMessage());
}

echo("\n--------------( Create [Campaign ID + Shipping + > 1 Item] )--------------\n");
try {
	$order = Printfection_Order::create(array(
			'campaign_id' => 26,
			'ship_to' => array(
					'name' => 'Zachary Flower',
					'address' => '2262 Bristol Street',
					'city' => 'Superior',
					'state' => 'Colorado',
					'zip' => '80027',
					'country' => 'US',
					'email' => 'zach@ninjaninja.net'
				),
			'lineitems' => array(
					array(
							'item_id' => 76,
							'size_id' => 4921,
							'quantity' => rand(1,100)
						),
					array(
							'item_id' => 82,
							'size_id' => 0,
							'quantity' => rand(1,100)
						)
				)
		));
	echo($order);
} catch (Exception $e) {
	echo($e->getMessage());
}

echo("\n--------------( Place )--------------\n");
try {
	$place = $order->place();
	echo($place);
} catch (Exception $e) {
	echo($e->getMessage());
}

echo("\n--------------( Create [Campaign ID Only] )--------------\n");
$order = Printfection_Order::create(array(
		'campaign_id' => 26
	));
echo($order);

echo("\n--------------( Add Shipping )--------------\n");
$order->ship_to = array(
		'name' => 'Zachary Flower',
		'address' => '2262 Bristol Street',
		'city' => 'Superior',
		'state' => 'Colorado',
		'zip' => '80027',
		'country' => 'US',
		'email' => 'zach@ninjaninja.net'
	);
$order->save();
echo($order);

echo("\n--------------( Add Item )--------------\n");
$order->lineitems = array(
		array(
			'item_id' => 76,
			'size_id' => 4921,
			'quantity' => rand(1,100)
		)
	);
$order->save();
echo($order);

echo("\n--------------( Change Item Quantity )--------------\n");
$lineitem = $order->lineitems[0];
$lineitem->quantity = rand(100,200);
$lineitem->save();
echo($order);

echo("\n--------------( Delete Item )--------------\n");
$lineitem->delete();
echo($order);

echo("\n--------------( Add Item )--------------\n");
$order->lineitems = array(
		array(
			'item_id' => 76,
			'size_id' => 4921,
			'quantity' => rand(1,100)
		)
	);
$order->save();
echo($order);

echo("\n--------------( Add 2nd Item )--------------\n");
try {
	$order->lineitems = array(
			array(
				'item_id' => 76,
				'size_id' => 4921,
				'quantity' => rand(1,100)
			)
		);
	$order->save();
	echo($order);
} catch (Exception $e) {
	echo($e->getMessage());
}

exit;