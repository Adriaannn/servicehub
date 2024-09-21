<?php include 'includes/session.php'; ?>
<?php
    $slug = $_GET['category'];

    $conn = $pdo->open();

    try{
        $stmt = $conn->prepare("SELECT * FROM category WHERE cat_slug = :slug");
        $stmt->execute(['slug' => $slug]);
        $cat = $stmt->fetch();
        $catid = $cat['id'];
    }
    catch(PDOException $e){
        echo "There is some problem in connection: " . $e->getMessage();
    }

    $pdo->close();

?>

<?php include 'includes/header.php'; ?>
<script>
    var userRating = 0; // Initialize the user rating variable 
    
    function rate(rating = 5) {   
        userRating = rating; // Set the user rating to the selected value
        
        // Change the color of the third star to yellow when the user clicks it
        if (rating == 1) { 
        document.getElementById("firstStar").classList.add("checked");
        document.getElementById("secondStar").classList.remove("checked");
        document.getElementById("thirdStar").classList.remove("checked");
        document.getElementById("forthStar").classList.remove("checked");
        document.getElementById("fifthStar").classList.remove("checked");
        } else if (rating == 2) { 
        document.getElementById("firstStar").classList.add("checked");
        document.getElementById("secondStar").classList.add("checked");
        document.getElementById("thirdStar").classList.remove("checked");
        document.getElementById("forthStar").classList.remove("checked");
        document.getElementById("fifthStar").classList.remove("checked");
        } else if (rating == 3) { 
        document.getElementById("firstStar").classList.add("checked");
        document.getElementById("secondStar").classList.add("checked");
        document.getElementById("thirdStar").classList.add("checked");
        document.getElementById("forthStar").classList.remove("checked");
        document.getElementById("fifthStar").classList.remove("checked");
        } else if (rating == 4) { 
        document.getElementById("firstStar").classList.add("checked");
        document.getElementById("secondStar").classList.add("checked");
        document.getElementById("thirdStar").classList.add("checked");
        document.getElementById("forthStar").classList.add("checked");
        document.getElementById("fifthStar").classList.remove("checked");
        } else if (rating == 5) { 
        document.getElementById("firstStar").classList.add("checked");
        document.getElementById("secondStar").classList.add("checked");
        document.getElementById("thirdStar").classList.add("checked");
        document.getElementById("forthStar").classList.add("checked");
        document.getElementById("fifthStar").classList.add("checked");
        }  
        $('#rate').val(rating); 
  }
</script>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
     
    <div class="content-wrapper">
        <div class="container">

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-sm-9">
                        <h1 class="page-header"><?php echo $cat['name']; ?></h1>
                        <div class="row">
                            <?php
                        
                            $conn = $pdo->open();
                        
                            try{
                                $inc = 0;   
                                $description = "";
                                $stmt = $conn->prepare('SELECT products.*, users.firstname, users.photo AS seller_photo, products.photo AS product_photo, IF(job_order.rate > 0, IF(job_order.rate > 5, AVG(job_order.rate), job_order.rate), 5) AS rate 
                                    FROM products 
                                    LEFT JOIN users ON products.seller_id = users.id 
                                    LEFT JOIN job_order ON products.id = job_order.product_id 
                                    WHERE products.category_id = :catid 
                                    AND products.status = "A" 
                                    GROUP BY products.id 
                                    ORDER BY products.id DESC');
                                $stmt->execute(['catid' => $catid]); 
                             
                                foreach ($stmt as $row) {
                                    if($row['name'] != ""){
                                        $image = (!empty($row['product_photo'])) ? 'images/'.$row['product_photo'] : 'images/noimage.jpg'; // change photo to product_photo
                                        if (strlen($row['description']) > 100) { 
                                            $description = substr($row['description'], 0,30) . " ...";
                                        }

                                        $start_1 = $row['rate'] >= 1 ? 'checked' : '';
                                        $start_2 = $row['rate'] >= 2 ? 'checked' : '';
                                        $start_3 = $row['rate'] >= 3 ? 'checked' : '';
                                        $start_4 = $row['rate'] >= 4 ? 'checked' : '';
                                        $start_5 = $row['rate'] == 5 ? 'checked' : '';
                            ?>
                            <div class="col-sm-6 col-xs-6 col-md-4">
                                <a href="product.php?product=<?php echo $row['slug']; ?>">
                                    <div class="box box-solid">
                                        <div class="box-body prod-body">
                                            <h5><?php echo $row['firstname']; ?></h5>
                                            <div style="display:flex;justify-content:center">
                                                <img src="<?php echo $image; ?>" width="100%" height="110px" class="thumbnail rounded-circle" >
                                            </div>
                                            <h5><?php echo $row['name']; ?></h5>
                                            <div class="rate">
                                                <span class="fa fa-star <?php echo $start_1; ?>" id="firstStar"></span>
                                                <span class="fa fa-star <?php echo $start_2; ?>" id="secondStar"></span>
                                                <span class="fa fa-star <?php echo $start_3; ?>" id="thirdStar"></span>
                                                <span class="fa fa-star <?php echo $start_4; ?>" id="forthStar"></span>
                                                <span class="fa fa-star <?php echo $start_5; ?>" id="fifthStar"></span>
                                            </div>
                                            <p style="text-overflow: ellipsis;"><?php echo $description; ?></p>
                                        </div>
                                        <div class="box-footer">
                                            <b>PHP <?php echo number_format($row['price'], 2); ?></b>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php 
                                        }
                                        $inc++;
                                    }
                                }
                                catch(PDOException $e){
                                    echo "There is some problem in connection: " . $e->getMessage();
                                }

                                $pdo->close();

                            ?> 
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <?php include 'includes/sidebar.php'; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
<style>
    .checked{
        color: orange;
    }
</style>
</body>
</html>
