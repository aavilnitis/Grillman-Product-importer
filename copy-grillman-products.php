<?php
/**
 * Plugin Name: Product XML Copier (Grillman -> Gatavo Dabā)
 * Description: Wordpress plugin that copies all products along with their data from supplier live XML file
 * Version: 1.0.2
 * Author: Aleksis Vilnitis
**/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function grillman_kopetajs_atjaunots()
{
    // Path to the XML file in the plugin directory
    $xml_path = plugin_dir_path(__FILE__) . 'grillman.xml';

    // Load the XML file
    $xml = simplexml_load_file($xml_path);

    foreach ($xml->product as $product) {
        $name = (string) $product->{'name-lt'};
        $description = (string) $product->{'description-lt'};
        $price = (string) $product->price;
        $sku = (string) $product->reference;
        $weight = (string) $product->weight;
        $length = (string) $product->depth;
        $width = (string) $product->width;
        $height = (string) $product->height;

        // Check if any value is null
        if (empty($name) || empty($description) || empty($price) || empty($sku) || empty($weight) || empty($length) || empty($width) || empty($height)) {
            echo 'Skipped product: ' . $name . ' (One or more values are null)<br>';
            continue;
        }

        $new_product = new WC_Product();
        $new_product->set_name($name);
        $new_product->set_description($description);
        $new_product->set_regular_price($price);
        $new_product->set_sku($sku);
        $new_product->set_weight($weight);
        $new_product->set_length($length);
        $new_product->set_width($width);
        $new_product->set_height($height);
        $new_product_id = $new_product->save();

        // Add the first image from the XML file
        if (isset($product->images) && isset($product->images->image)) {
            $image_url = (string) $product->images->image[0];
            $image_id = media_sideload_image($image_url, 0, null, 'id');
            set_post_thumbnail($new_product_id, $image_id);
        }
        $old_id = (string) $product->category_default_id;

        // Set the main category
        $category_id = getCategory($old_id);
		if(!empty($category_id)){
			wp_set_object_terms($new_product_id, $category_id, 'product_cat');
		}
        
        echo 'Added product: ' . $new_product->get_name() . ' (ID: ' . $new_product_id . ')<br>';
    }
}


function kopetaja_shortcode() {
    ob_start();
    grillman_kopetajs_atjaunots();
    return ob_get_clean();
}
add_shortcode('kopet_produktus', 'kopetaja_shortcode');

function getCategory($id) {
    $mapping = array(
        15 => 95,
        40 => 96,
        91 => 97,
        61 => 98,
        93 => 99,
        44 => 100,
        94 => 101,
        49 => 102,
        92 => 103,
        23 => 104,
        5 => 105,
        108 => 106,
        109 => 107,
        110 => 108,
        30 => 109,
        46 => 110,
        105 => 111,
        36 =>112,
        10 =>113,
        16 =>114,
        14 =>115,
        39 =>116,
        43 =>117,
        42 =>118,
        31 =>119,
        45 =>120,
        21=>121,
        68=>122,
        38=>123,
        53=>124,
        8=>125,
        60=>126,
        17=>127,
        62=>128,
        26=>129,
        6=>130,
        41=>131,
        12=>132,
        103=>133,
        67=>134,
        2=>135,
        111=>136, 
        51=>137, 
        159=>138
    );
    return $mapping[$id];
}