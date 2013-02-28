<?
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:../edp.sqlite3');
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
   
    // Prepare INSERT statement to SQLite3 memory db
    //$insert = "INSERT INTO modeldb (vendor, serie, model) VALUES ('IBM', 'thinkpad', 'R40')";
    //$stmt = $file_db->prepare($insert);
    //$stmt->execute();                        
       
    
    
    
    $result = $file_db->query("SELECT * FROM modeldb");
    
    foreach($result as $row) {
      echo "ID: $row[id] - Vendor: $row[vendor] - serie: $row[serie] - model: $row[model] - modelfolder: $row[modelfolder]<br>";
    }


    $result = $file_db->query("SELECT * FROM ethernet");
    
    foreach($result as $row) {
      echo "ID: $row[id] - name: $row[name]<br>";
    }
    
                        
?>

    
     
