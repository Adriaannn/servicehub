<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">
<script>
    var userRating = 0; // Initialize the user rating variable 
     
    function rate(rating = 3) {   
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
    <?php include 'includes/navbar.php'; ?>
     
      <div class="content-wrapper">
        <div class="container">

          <!-- Main content -->
          <section class="content">
            <div class="row">
                <div class="col-sm-9">
                    <?php
                        if(isset($_SESSION['error'])){
                            echo "
                                <div class='alert alert-danger'>
                                    ".$_SESSION['error']."
                                </div>
                            ";
                            unset($_SESSION['error']);
                        }
                    ?>
                    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                          <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                        </ol>
                        <div class="carousel-inner">
                          <div class="item active">
                            <!--<img src="images/banner2.png" alt="Second slide">-->
                          </div>
                        </div>
                        <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                          <span class="fa fa-angle-left"></span>
                        </a>
                        <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                          <span class="fa fa-angle-right"></span>
                        </a>
                    </div>
                    <h2>Monthly Top Sellers</h2>
                    <?php
                        $conn = $pdo->open();
                        $description = "";

                        try{
                            $inc = 0;   
                            $stmt = $conn->prepare("SELECT products.*, users.firstname, users.photo AS seller_photo, IF(job_order.rate > 0, IF(job_order.rate > 5, AVG(job_order.rate), job_order.rate), 5) AS rate FROM products LEFT JOIN users ON products.seller_id = users.id LEFT JOIN job_order ON products.id = job_order.product_id WHERE products.status = 'A' GROUP BY products.id ORDER BY rate DESC");
                            $stmt->execute();
 
                            echo "<div class='row'>";
                            foreach ($stmt as $row) {
                                $prodImage = (!empty($row['photo'])) ? 'images/'.$row['photo'] : 'images/noimage.jpg';
                                if (strlen($row['description']) > 100) { 
                                    $description = substr($row['description'], 0, 30) . " ...";
                                }

                                $start_1 = $row['rate'] >= 1 ? 'checked' : '';
                                $start_2 = $row['rate'] >= 2 ? 'checked' : '';
                                $start_3 = $row['rate'] >= 3 ? 'checked' : '';
                                $start_4 = $row['rate'] >= 4 ? 'checked' : '';
                                $start_5 = $row['rate'] == 5 ? 'checked' : '';

                                echo "
                                    <a href='product.php?product=".$row['slug']."'>
                                        <div class='col-sm-6 col-xs-6 col-md-4'>
                                            <div class='box box-solid'>
                                                <div class='box-body prod-body'>
                                                    
                                                    <h5>".$row['firstname']."</h5> 
                                                    <div style='display:flex;justify-content:center'>
                                                        <img src='".$prodImage."' width='100%' height='110px' class='thumbnail rounded-circle' > 
                                                    </div> 
                                                    <h5>".$row['name']."</h5> 
                                                    <div class='rate'>
                                                        <span class='fa fa-star ".$start_1."' id='firstStar'></span>
                                                        <span class='fa fa-star ".$start_2."'  id='secondStar'></span>
                                                        <span class='fa fa-star ".$start_3."'  id='thirdStar'></span>
                                                        <span class='fa fa-star ".$start_4."'  id='forthStar'></span>
                                                        <span class='fa fa-star ".$start_5."'  id='fifthStar'></span>
                                                    </div>  
                                                    <p style='text-overflow: ellipsis;'>". 
                                                        $description
                                                    ."</p>
                                                </div>
                                                <div class='box-footer'>
                                                    <b>PHP ".number_format($row['price'], 2)."</b>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <script> 
                                        rate(".$row['rate'].");
                                    </script>
                                "; 
                                $inc++;
                            }
                            echo "</div>";
                            if($inc % 3 != 0) {
                                echo "<div class='col-sm-".(3 - ($inc % 3))."'></div>";
                            }
                        }
                        catch(PDOException $e){
                            echo "There is some problem in connection: " . $e->getMessage();
                        }

                        $pdo->close();
                    ?> 
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
