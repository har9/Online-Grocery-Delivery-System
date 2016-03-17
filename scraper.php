<?php
 
    function createDatabase(){
	$servername = "localhost";
	$username = "root";
    $password = "root";
    $database = "testgrocery";
        
        $conn = new mysqli($servername, $username, $password);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "CREATE DATABASE if not exists myDB";
        if ($conn->query($sql) === TRUE) {
              echo "Database created successfully";
        } else{
            echo "query error";
        }
        
      
        
        $conn->close();    
    }
    function createTables(){
	$servername = "localhost";
	$username = "root";
    $password = "root";
    $database = "testgrocery";
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        //create category
         $createProductCategory="create table productcategory(
        categoryid int(10) UNSIGNED AUTO_INCREMENT,
        category varchar(40) NOT NULL,
        subcategory varchar(40) not null,
        PRIMARY KEY(categoryid)
        )ENGINE=INNODB";
         //create product Table
        $createProduct = "create table product(
            productid INT(10) UNSIGNED AUTO_INCREMENT,
            productname varchar(50),
            imageurl varchar(255),
            price FLOAT UNSIGNED,
            categoryid INT(10) unsigned,
            isonsale BOOLEAN,
            saleprice FLOAT unsigned,
            taxable BOOLEAN,
            soldcount int(10) unsigned DEFAULT 0,
            PRIMARY KEY (productid),
            FOREIGN KEY (categoryid) references productcategory(categoryid)
        )ENGINE=INNODB";
//FOREIGN KEY (categoryid) references productcategory(categoryid)
            
 $createCustomer="create table customer(
         customerid int(10) unsigned auto_increment,
         name varchar(30),
         age int(3),
         sex varchar(1),
         password varchar(80),
         balance float,
         paymentflag boolean,
         creditcardnumber varchar(10),
         ccexpiration datetime,
         bankaccountnumber varchar(20),
         bankroutingnumber varchar(20),
         address varchar(50),
         phonenumber varchar(10),
         email varchar(40),
         status varchar(10),
         currentbasket int(10),
         primary key(customerid)   
        )ENGINE=INNODB";
        //create basket
        $createBasket = "create table basket(
            basketid INT(10) UNSIGNED AUTO_INCREMENT,
            customerid INT(10) UNSIGNED,
            isstandingorder BOOLEAN,
            haltstandingorder BOOLEAN,
            basketdate DATETIME,
            PRIMARY KEY (basketid),
            FOREIGN KEY (customerid) references customer(customerid)
            
        )ENGINE=INNODB";
        
        $createBasketItem="create table basketitem(
            basketid INT UNSIGNED,
            productid INT UNSIGNED,
            productquantity INT UNSIGNED,
            FOREIGN KEY (basketid) references basket(basketid),
            FOREIGN KEY (productid) references product(productid)
        )ENGINE=INNODB";
       
        $createAdministrator="create table administrator(
        adminid int(10) unsigned auto_increment,
        name varchar(30),
        password varchar(30),
        address varchar(50),
        phonenumber varchar(10),
        email varchar(40),
        primary key(adminid)
        )ENGINE=INNODB";
        
        $createTransaction="create table transaction(
            transactionid int(10) unsigned auto_increment,
            basketid int(10) unsigned,
            customerid int(10) unsigned,
            transactiontotal float,
            acceptflag boolean,
            ordertime DATETIME,
            estimatetimeofarrival datetime,
            deliverydate DATETIME,
            primary key(transactionid, basketid),
            foreign key(basketid) references basket(basketid),
            foreign key(customerid) references customer(customerid)
        )ENGINE=INNODB";
        
        $createDeliveryPricing="create table deliverypricing(
            rangestart int(10),
            rangeend int(10),
            price float,
            primary key(rangestart, rangeend)
        )ENGINE=INNODB";
        
        $createBill="create table bill(
            billid int(10) unsigned auto_increment,
            transactionid int(10) unsigned,
            totalitemcost float,
            costwithtax float,
            deliverycharge float,
            tip float,
            paidflag boolean,
            primary key(billid),
            foreign key(transactionid) references transaction(transactionid)
        )ENGINE=INNODB";
        
        $createDeliveryPerson="create table deliveryperson(
            dpid int(10) unsigned auto_increment,
            name varchar(30),
            address varchar(50),
            workstart varchar(20),
            workend varchar(20),
            salary float,
            curlocation varchar(50),
            curroute varchar(40),
            primary key(dpid)
        )ENGINE=INNODB";
       
       //correct so far
        $createDispatchTicket="create table dispatchticket(
         transactionid int(10) unsigned,
         customerid int(10) unsigned,
         basketid int(10) unsigned,
         dpid int(10) unsigned,
         customeraddress varchar(50),
         dateandtime DATETIME,
         primary key(transactionid, dateandtime),
         foreign key(transactionid) references transaction(transactionid),
         foreign key(customerid) references customer(customerid),
         foreign key(basketid) references basket(basketid),
         foreign key(dpid) references deliveryperson(dpid)
        )ENGINE=INNODB";
        
    /* if ($conn->query($createTestTable) === TRUE) {
      echo "Table MyGuests created successfully";
    }
     
     $insertThing = "insert into testTable (firstname, lastname) values ('sick', 'hobo')";
     if ($conn->query($insertThing) === TRUE) {
      echo "Table MyGuests created successfully";
    } else {
      echo "Error creating table: " . $conn->error;
    }   */
    
        queryCreate($conn, $createProductCategory, "productcategory");
        queryCreate($conn, $createProduct ,"product");
        queryCreate($conn, $createCustomer, "customer");
        queryCreate($conn, $createBasket, "basket");
        queryCreate($conn, $createBasketItem, "basketitem"); 
        queryCreate($conn, $createAdministrator, "administrator");
        queryCreate($conn, $createTransaction, "transaction");
        queryCreate($conn, $createDeliveryPricing, "deliverypricing");
        queryCreate($conn, $createBill, "bill");
        queryCreate($conn, $createDeliveryPerson, "deliverypricing");
        queryCreate($conn, $createDispatchTicket, "dispatchticket");
   
        $conn->close();
    }
    
    //helper for creating table
    function queryCreate($conn, $query, $name){
        if ($conn->query($query) === TRUE) {
         echo "Table $name created successfully\n";
        }else{
            echo "Table $name not created successfully\n";
        }
        
    }
    
    //main scraper function
    function scrapeMasterMarkK(){
         $homepage = file_get_contents('http://www.harrisfarm.com.au/');
    
        //$processedHomePage =  htmlspecialchars($homepage, ENT_COMPAT, "UTF-8", false);
        //$reverseProc = htmlspecialchars_decode($processedHomePage);
        //echo $processedHomePage;
        
        //get the navigator bar
       
       // $pattern = '/collections(\/[\w-]*)*/i';//'/collections\/[\w-]*/i';
        $pattern = '/collections([\w\-&\/]*)/i';
        
       preg_match_all($pattern, $homepage, $collections);
      array_pop($collections); // taking off the second part of the array which doesn't contain collection and with "/"
      print_r($collections); //everything scraped only 2-10 is needed for our purpose
     
       $patterntype = '/[\w-&]*$/i';
    
     
       $subSiteName = array(); //containing subsite names
       $typeArr = array(); //containing type names
       for($x=2; $x<10; $x++){
         array_push($subSiteName, $collections[0][$x] ); //push subsite name
         
         preg_match_all($patterntype, $collections[0][$x], $type);
         
         $typeArr[$type[0][0]] = array();//associative, empty array
         
       }
       echo "<p>subsite name: </p>";
      print_r($subSiteName);
       echo "<p>type names: </p>";
      print_r($typeArr);
       echo "<p> </p>";
       
       //populate subcategories here
       //
       //reserved for code populating subcategories
       //, $type[0][0] are the subtypes
       //fruit subcategory
       
       
       
       $subsubsite=array(); //containing site address for the fruit
       for($x=15; $x<=28; $x++){
        preg_match_all($patterntype, $collections[0][$x], $type); //getting te name of the type, it will be under $type[0][0] #NOT THE SUBSITE ADDRESS
        //name of the subsite address is $collections[0][$x]
        //create an associate array with name of the type and it points to the subsite address
        $subsubsite[$type[0][0]] = $collections[0][$x];
         
        //array_push($typeArr['fruit'], $type[0][0]);
        //array_push($typeArr['fruit'], array());
        //$typeArr['fruit'] = ""
        //array_push($typeArr['fruit'])
       }
       array_push($typeArr['fruit'], $subsubsite); //populate fruit and its subtype
       
       
       //veggies subcategory
       $subsubsite = array();
       for($x=31; $x<=48; $x++){
        preg_match_all($patterntype, $collections[0][$x], $type);
      
         $subsubsite[$type[0][0]] = $collections[0][$x];
       }
      array_push($typeArr['vegies'], $subsubsite); //populate vegies and its subtypes
       //print_r($typeArr);
       //groceries subcategory
       $subsubsite = array();
       for($x=51; $x<=70; $x++){
        preg_match_all($patterntype, $collections[0][$x], $type);
      
         $subsubsite[$type[0][0]] = $collections[0][$x];
       }
             array_push($typeArr['groceries'], $subsubsite); //groceries population
       //fridge subcategory
              $subsubsite = array();
       for($x=73; $x<=84; $x++){
                   preg_match_all($patterntype, $collections[0][$x], $type);
      
         $subsubsite[$type[0][0]] = $collections[0][$x];
       }
       array_push($typeArr['fridge'], $subsubsite); //fridges
        //butcher subcategory
               $subsubsite = array();
        for($x=87; $x<=96; $x++){
        preg_match_all($patterntype, $collections[0][$x], $type);
      
         $subsubsite[$type[0][0]] = $collections[0][$x];
       }
       array_push($typeArr['butcher'], $subsubsite); //
        //seafood subcategory
               $subsubsite = array();
        for($x=99; $x<=107; $x++){
        preg_match_all($patterntype, $collections[0][$x], $type);
      
         $subsubsite[$type[0][0]] = $collections[0][$x];
       }
       array_push($typeArr['seafood'], $subsubsite); //
        //organic subcategory
               $subsubsite = array();
         for($x=109; $x<=113; $x++){
        preg_match_all($patterntype, $collections[0][$x], $type);
      
         $subsubsite[$type[0][0]] = $collections[0][$x];
       }
       array_push($typeArr['organic'], $subsubsite); //
        //wholesale subcategory
               $subsubsite = array();
        for($x=116; $x<=120; $x++){
        preg_match_all($patterntype, $collections[0][$x], $type);
      
         $subsubsite[$type[0][0]] = $collections[0][$x];
       }
    array_push($typeArr['wholesale'], $subsubsite); //

    print_r($typeArr); //printing the filled associative array
     insertProductTypes($typeArr);  //populate the sql database PRODUCTCATEGORY, THIS IS THE RIGHT CODE
    
    //scanning through the associative array and find the category id of each combinations
    echo "<p></p>";
    foreach($typeArr as $gk => $gk_value){
        //echo " " . $gk . " ";  //$gk is the grandkey
        //print_r($gk_value); echo "<p></p>";
        foreach($gk_value[0] as $sk => $sk_value){
            //echo " " . $sk . " "; //$sub key is the subtype
            //gk and sk are grandkey and subkey
           // echo "subvalue " . $sk_value;
           // echo "<br>";
          
            $address = 'http://www.harrisfarm.com.au/'.$sk_value;
            //echo $address;
            //echo "<br>";
            //TODO find category id and scan and insert the products
            
            if(intval(cidGetter($gk, $sk)) != -1){
                scanProducts(intval(cidGetter($gk, $sk)), $address);  //insert the individual product
            }
            
            
            
        }
      //  echo "<p></p>";
    }
    
    
    //scanProducts(intval(cidGetter('fruit', 'apples')), 'http://www.harrisfarm.com.au/collections/apples'); //only scan one page
    }
    
    //returns categoryid based on the grandkey and subkey, return -1 if no such item exist
    function cidGetter($grandkey, $subkey){
	$servername = "localhost";
	$username = "root";
    $password = "root";
    $database = "testgrocery";
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
       
        // Check connection
        $sql = "SELECT categoryid, category, subcategory FROM productcategory where category='$grandkey' and subcategory='$subkey'";
        
        $result = $conn->query($sql);

        //
   if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
         //echo "<br> id: ". $row["categoryid"]. " - Name: ". $row["category"]. " subcategory" . $row["subcategory"] . "<br>";
         $conn->close();
         return $row["categoryid"];
     }
} else {
     
     $conn->close();
     return -1;
}
        //
        
        
    }
    //take product type and webaddress
    //one page ONLY! returns nothing
    function scanProducts($categoryid, $webAddress){
        //open connection
	$servername = "localhost";
	$username = "root";
    $password = "root";
    $database = "testgrocery";
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        
         $currentAddress = file_get_contents($webAddress);
        // echo "<p>this is web address:" . $webAddress . "this is categoryid" . $categoryid. "</p>";
     
         $pattern = '{<div\sclass=\"imgcont\sloadingimg\">\s*<a\shref=\"[\w\-\/]*\"\sclass=\"image-inner-wrap\s*\">\s*<img\s*src=\"[\w\.\/\?=\-]*\"\s*alt=\"[\w\s\(\-\)]*\"\s*\/>\s*<div\sclass=\"salecontainer\">\s*<span\sclass=\"sale\">\s*(<small>from<\/small>\s*|)\$\d*\.\d*}i';  //work on this pattern
         preg_match_all($pattern, $currentAddress, $chunk);
        //chunk[0][index] has all the contents
          //get product name and the product price for each name
          $patternProductName = '{alt=\"[\w\s\(\-\)]*}i';
         // $patternProductPrice = '{\$\d*\.\d*$}i';  //with dollar sign
          $patternProductPrice = '{\d*\.\d*$}i';  //without dollar sign
          $patternProductImageurl = '{img\s*src=\"[\w\.\/\?=\-]*}i';
          for($x = 0; $x< 1; $x++){
            for($y=0; $y< count($chunk[$x]); $y++){
                
               // echo "Index x: " . $x . "subIndex: ". $y . "</p>";
                //echo "<p> </p>";
                //echo htmlspecialchars($chunk[$x][$y]);
                //get the name chunk
                preg_match_all($patternProductName, $chunk[$x][$y], $nameChunk); //should match a name string
                //echo "<p> </p> nameString: ";
                //print_r( substr($nameChunk[0][0], 5)); //name string under substr($nameChunk[0][0], 5)
                $namestring1 = substr($nameChunk[0][0], 5);
                //get the price chunk
                preg_match($patternProductPrice, $chunk[$x][$y], $priceChunk); //should match a price chunk
                //echo "<p> </p> priceString: ";
                //print_r(floatval($priceChunk[0])); //price string under $priceChunk[0]
                $pricestring1 = floatval($priceChunk[0]);
                //get the image url, source is from shopify
               //echo "<p> </p>imageURL: ";
               preg_match_all($patternProductImageurl, $chunk[$x][$y], $imageUrl); //should match the image url
               //print_r(substr($imageUrl[0][0], 9)); //imageurl is in substr($imageUrl[0][0], 9)
               $urlstring = substr($imageUrl[0][0], 9); 
               //TODO insert the product into the product category 
               //construct sqlcommand for insertion
               
               $singleProductInsert = "INSERT INTO product (productname, imageurl, price, categoryid, isonsale, saleprice, taxable ) VALUES ('$namestring1','$urlstring', '$pricestring1', '$categoryid' ,false,'$pricestring1',true)";
               //insertion
               insertProduct($conn, $singleProductInsert);
               
            }
          }
          
          
           $conn->close();
    }
    function insertProduct($conn, $sqlcommand){
        
        
        if ($conn->query($sqlcommand) === TRUE) {
             echo "New product record created successfully";
             } else {
              echo "Error: " . $sqlcommand . "<br>" . $conn->error;
              echo "<br>";
            }
    
       
    }
    
    function insertProductTypes($associativeArrayArray){
        //open connection
	$servername = "localhost";
	$username = "root";
    $password = "root";
    $database = "testgrocery";
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        
        foreach($associativeArrayArray as $x => $x_value) { //grand type
          //construct query string
          foreach($associativeArrayArray[$x][0] as $y => $y_value){ //subtype $y is the key
             $insertion = "INSERT INTO productcategory (category, subcategory) values ('$x', '$y')";
             
            if ($conn->query($insertion) === TRUE) {
             echo "New producttype record created successfully";
             } else {
              echo "Error: " . $insertion . "<br>" . $conn->error;
              echo "<br>";
             }
          }
        
        }
        //close the connection
        $conn->close();
    }
    //populate the items
    function display(){
        echo "<p>i am a monster</p>";
    }
    
    
    //function for creating the database and populating the products
    function estabConnect(){
        //main
        createDatabase();
        createTables();
        //populate database
        scrapeMasterMarkK();
        
    }
    
    function insert($sql){
         //open connection
	$servername = "localhost";
	$username = "root";
    $password = "root";
    $database = "testgrocery";
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        if ($conn->query($sql) === TRUE) {
             echo "New record created successfully";
             } else {
              echo "Error: " . $sql . "<br>" . $conn->error;
              echo "<br>";
             }
    }
    
    //takes productid and the number of sold
    function updateProductCount($pid, $num){
            //open connection
	$servername = "localhost";
	$username = "root";
    $password = "root";
    $database = "testgrocery";
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        //retrieve the product with product id, and get the soldcount
        $sqlcommand = "SELECT productid, soldcount from product where productid='$pid'";
        $result = $conn->query($sqlcommand);
        if ($result->num_rows > 0) {
             while($row = $result->fetch_assoc()) {
            echo "<br> pid: ". $row["productid"]. " - soldcount: ". $row["soldcount"] . "<br>";
            if(is_int(intval($row["soldcount"]))=== true){
                echo "sold count is an int";
            }else{
                echo "sold count is not an int";
            }
            //increment the sold count base on the $num
            $newCount = intval($row["soldcount"])+$num;
            echo "newCount" . $newCount;
            
              //update the new sold count associated with the product id
              
            $updatecommand = "update product set soldcount= '$newCount' where productid = '$pid'";
            if ($conn->query($updatecommand) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}
           
     }
            
        }else{
            echo "no such product exist";
        }
        
        $conn->close();
        
        
        
      
        
        
        
    }
    //generates best and worst report
    function populateBestWorstReport(){
        //get the current
     //open connection
	$servername = "localhost";
	$username = "root";
    $password = "root";
    $database = "testgrocery";
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $curworstnum = 100000; //default is -1 so it can be receptive to every other numbers
        $worstArray = array();  //array that contains the names of the worst selling item
        
        $curbestnum = -1;
        $bestArray = array();
        $iterStop = 11;
        $iterNow = 1;
        //sql command for getting the product id, name and sold count
        $sqlcommand = "select distinct productid, soldcount, productname from product";
         $result = $conn->query($sqlcommand);
          if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
            
                
                //echo "<br> pid: ". $row["productid"]. " - soldcount: ". $row["soldcount"] . "<br>";
                //intval($row["soldcount"]) // sold count, type int
                //$row['productid']  // type string
                //$row['productname'] // type string
                
               // echo "product name: " . $row['productname'] . " productid: " . $row['productid'] . "sold count: " . $row["soldcount"] . "<br>";
                $tempVal = $curworstnum;  //store the current worst number
                    
                if($curworstnum > intval($row["soldcount"])){
                 //   echo "<br>soldcount" . intval($row["soldcount"]) . " ";
                    $curworstnum = intval($row["soldcount"]); // current worst number if now the  intval number
                   // echo "worstnum now:  " . $curworstnum . "<br>";
                    //discard the curworst array accumulated so far
                    $worstArray = array();
                    
                    //put the recent name into the curworst array
                    array_push($worstArray, $row['productname']);
                    
                }else if($curworstnum == intval($row["soldcount"])){
                    array_push($worstArray, $row['productname']);
                }
                
                if($curbestnum < intval($row["soldcount"]) ){
                    //curbestnum is lesser than the cur num, thus replace
                    $curbestnum = intval($row["soldcount"]);
                    
                    //clear the curbest num array
                    $bestArray = array();
                    array_push($bestArray, $row["productname"]);
                }else if($curbestnum == intval($row["soldcount"])){
                    array_push($bestArray, $row["productname"]);
                    
                }
                
                
                
                //stop pre-emptively for my case because i want it to stop and not going forever
                $iterNow +=1;
                if($iterStop == $iterNow){
                    break;
                }
                
            }
           
            //this cannot be allowed, if the best num and the worst num are the same, then
            //worstarray are erased.
            if($curbestnum = $curworstnum){
                $worstArray = array();
            }
            
            echo "<h2>The Following Are The Best Selling and the Worst Selling Items: </h2>";
             echo "<br>Worst Selling:";
             for($x = 0; $x< count($worstArray); $x++){
                
                echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $worstArray[$x] . " ";
             }
             echo "<br>Best Selling";
             for($x = 0; $x < count($bestArray); $x++){
                 echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $bestArray[$x] . " ";
             }
           
          }
        
        
        
        
        $conn->close();
        
    }
    //generates average basket value report, take a param base on how many days. so for example 1 day or 200 days. upper range is not restricted
    
    function averageBasketValueReport($mDay){
        echo "i got here like a champ";
        //TODO calculate currentdate-mday
        
        //TODO retrieve the basketid that has lesser than current day, but greater than currentdate-mDay
        
        //TODO get the total number of basketids for average calculation
        
        //TODO get the basketItems of each basketid and get total associated with each basketid
        
        //
        
    }
    //estabConnect();  //this is the correct code. REENABLE for database repopulation
    
    //creating product category
    
        
?>