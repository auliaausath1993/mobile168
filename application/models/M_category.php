<?php

class M_category extends CI_Model{
    public function edit_price($data = array()){
        $result         = new stdClass;
        $categories     = $data['id_category'];
        $prices         = $data['update_harga'];
        $prices_type    = $data['tipe_update'];

        $successUpdate  = array();
        $notUpdated     = array();
        $errorUpdate    = array();
        //$this->db->trans_start();
        foreach($categories as $key => $category){
            // Check price nya diisi atau tidak. kalo diisi maka update
            if($prices[$key]){

            // Insert Product History 
              $rows  = $this->db->where('category_id',$category)->get('product')->result();
              if ($rows){
               foreach($rows as $row){
                $data = array('object_id'=>$row->id,'created'=>date('Y-m-d H:i:s'),'category_id'=>$category,'type'=>'product');
                $data['old_price'] = $row->price_production;
                if($prices_type[$key] == 'Persen'){
                 $data['new_price'] = $row->price_production + (($prices[$key] / 100)*$row->price_production);
             } else {
                 $data['new_price'] = $row->price_production + $prices[$key];
             }
             $datas[] = $data;
         }

     }

            // Update product
   //   if($prices_type[$key] == 'Persen'){
   //      $multiplyBy     = $prices[$key] / 100;
   //      $query          = "
   //      UPDATE product p 
   //      SET 
   //      price_production = price_production + (price_production * {$multiplyBy}),
   //      price_old        = price_old + (price_old * {$multiplyBy})
   //      WHERE category_id = {$category}
   //      ";




   //  }else{
   //      $query      = "
   //      UPDATE product p 
   //      SET 
   //      price_production = price_production + {$prices[$key]},
   //      price_old        = price_old + {$prices[$key]}
   //      WHERE category_id = {$category}
   //      ";


   //  }

   //  $this->db->query($query);
   //  if ($rows){
   //     $this->db->insert_batch('product_history',$datas);
   // }


    //Insert History Product Price
   $rows2  = $this->db->select('pp.id,pp.price')->from('product_price pp')->join('product p','p.id=pp.prod_id')->where('p.category_id',$category)->get()->result();
   if($rows2){
       foreach($rows2 as $row2){
        $data2 = array('object_id'=>$row2->id,'created'=>date('Y-m-d H:i:s'),'category_id'=>$category,'type'=>'product_price');
        $data2['old_price'] = $row2->price;
        if($prices_type[$key] == 'Persen'){
         $data2['new_price'] = $row2->price + (($prices[$key] / 100)*$row2->price);
     } else {
         $data2['new_price'] = $row2->price + $prices[$key];
     }
     $datas2[] = $data2;
 }
}



//$old_price  = $this->db->select('pp.id,pp.price,pp.old_price')->from('product_price pp')->join('product p','p.id=pp.prod_id')->where('p.category_id',$category)->get()->result();




            // Update product price
            if($prices_type[$key] == 'Persen'){
                $multiplyBy     = $prices[$key] / 100;
                $query = "
                UPDATE product_price pp
                JOIN product p ON p.id = pp.prod_id
                JOIN product_category pc ON p.category_id = pc.id
                SET
                pp.price = IF(pp.old_price = 0,pp.price + (pp.price * {$multiplyBy}), pp.price),
                pp.old_price = IF(pp.old_price > 0,pp.old_price + (pp.old_price * {$multiplyBy}), pp.old_price)
                WHERE category_id = {$category}
                ";
            }else{
                $query = "
                UPDATE product_price pp
                JOIN product p ON p.id = pp.prod_id
                JOIN product_category pc ON p.category_id = pc.id
                SET
                pp.price = IF(pp.old_price = 0,pp.price + {$prices[$key]}, pp.price),
                pp.old_price = IF(pp.old_price > 0,pp.old_price + {$prices[$key]}, pp.old_price)
                WHERE category_id = {$category}
                ";
            }

        
        
    
    $this->db->query($query);



if ($rows2) {
   $this->db->insert_batch('product_history',$datas2);
}


//Insert History Product Grosir
$rows3  = $this->db->select('hg.id,hg.price')->from('harga_grosir hg')->join('product p','p.id=hg.prod_id')->where('p.category_id',$category)->get()->result();
if($rows3){
   foreach($rows3 as $row3){
    $data3 = array('object_id'=>$row3->id,'created'=>date('Y-m-d H:i:s'),'category_id'=>$category,'type'=>'harga_grosir');
    $data3['old_price'] = $row3->price;
    if($prices_type[$key] == 'Persen'){
     $data3['new_price'] = $row3->price + (($prices[$key] / 100)*$row3->price);
 } else {
     $data3['new_price'] = $row3->price + $prices[$key];
 }
 $datas3[] = $data3;
}
$this->db->insert_batch('product_history',$datas3);
}

// Update product grosir
if($prices_type[$key] == 'Persen'){
    $multiplyBy     = $prices[$key] / 100;
    $query = "
    UPDATE harga_grosir hg
    JOIN product p ON p.id = hg.prod_id
    JOIN product_category pc ON p.category_id = pc.id
    SET hg.price = hg.price + (hg.price * {$multiplyBy})
    WHERE category_id = {$category}
    ";
}else{
    $query = "
    UPDATE harga_grosir hg
    JOIN product p ON p.id = hg.prod_id
    JOIN product_category pc ON p.category_id = pc.id
    SET hg.price = hg.price + {$prices[$key]}
    WHERE category_id = {$category}
    ";
}

$this->db->query($query);

//Insert History Product Grosir
if($rows3){
   $this->db->insert_batch('product_history',$datas3);
}

                
$successUpdate[] = array(
    'id_category'   => $category,
);

}else{
    $notUpdated[] = array(
        'id_category'   => $category,
        'info'          => "No prices"
    );

}
}
//$this->db->trans_complete();

$result->data       = array(
    'successUpdate' => $successUpdate,
    'errorUpdate'   => $errorUpdate,
    'notUpdated'    => $notUpdated
);
return $result;
}
}