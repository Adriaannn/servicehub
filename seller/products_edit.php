<?php
    include 'includes/session.php';
    include 'includes/slugify.php';

    if(isset($_POST['edit'])){
        $id = $_POST['id'];
        $name = $_POST['name'];
        $slug = slugify($name);
        $category = $_POST['category'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $photo = ''; // Default value if no file is uploaded

        // File upload logic
        if(isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photo_name = $_FILES['photo']['name'];
            $photo_tmp = $_FILES['photo']['tmp_name'];
            $photo_extension = pathinfo($photo_name, PATHINFO_EXTENSION);
            $photo = uniqid() . '.' . $photo_extension; // Only store the file name
            $target_file = '../images/' . $photo; // Full path to the destination file
            move_uploaded_file($photo_tmp, $target_file);
        }

        $conn = $pdo->open();

        try{
            $stmt = $conn->prepare("UPDATE products SET name=:name, slug=:slug, category_id=:category, price=:price, description=:description, photo=:photo WHERE id=:id");
            $stmt->execute(['name'=>$name, 'slug'=>$slug, 'category'=>$category, 'price'=>$price, 'description'=>$description, 'photo'=>$photo, 'id'=>$id]);
            $_SESSION['success'] = 'Product updated successfully';
        }
        catch(PDOException $e){
            $_SESSION['error'] = $e->getMessage();
        }
        
        $pdo->close();
    }
    else{
        $_SESSION['error'] = 'Fill up edit product form first';
    }

    header('location: products.php');
?>
