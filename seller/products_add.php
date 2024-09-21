<?php
include 'includes/session.php';
include 'includes/slugify.php';

if(isset($_POST['add'])){
    $name = $_POST['name'];
    $slug = slugify($name);
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $seller_id = $seller['id'];
    $status = 'P'; // Default for PENDING

    $conn = $pdo->open();

    $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM products WHERE slug=:slug AND seller_id=".$seller['id']);
    $stmt->execute(['slug'=>$slug]);
    $row = $stmt->fetch();

    if($row['numrows'] > 0){
        $_SESSION['error'] = 'Product already exists';
    }
    else{
        // Check if a file is selected for upload
        if(isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK){
            $filename = $_FILES['photo']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $new_filename = $slug.'.'.$ext;
            // Move the uploaded file to the destination directory
            move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$new_filename);
        }
        else{
            $new_filename = ''; // No file uploaded
        }

        try{
            $stmt = $conn->prepare("INSERT INTO products (category_id, name, description, slug, price, seller_id, status, photo) VALUES (:category, :name, :description, :slug, :price,:seller_id,:status, :photo)");
            $stmt->execute(['category'=>$category, 'name'=>$name, 'description'=>$description, 'slug'=>$slug, 'price'=>$price, 'seller_id'=>$seller_id,'status'=>$status, 'photo'=>$new_filename]);
            $_SESSION['success'] = 'Product added successfully';
        }
        catch(PDOException $e){
            $_SESSION['error'] = $e->getMessage();
        }
    }

    $pdo->close();
}
else{
    $_SESSION['error'] = 'Fill up the product form first';
}

header('location: products.php');
?>
