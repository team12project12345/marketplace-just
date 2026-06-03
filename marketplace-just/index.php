<?php
header("Content-Type: text/html; charset=UTF-8");

include "db.php";
mysqli_set_charset($conn, "utf8");

$result = mysqli_query($conn, "SELECT * FROM items");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Marketplace</title>

    
    <link rel="stylesheet" href="css/style.css?v=3">
</head>

<body>

<div class="container">
    <h2>Available Items</h2>

    <div class="cards">

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="product-card">

             
                <?php if (!empty($row['image'])) { ?>
                    <img src="uploads/<?php echo $row['image']; ?>" alt="item image">
                <?php } else { ?>
                    <div class="no-image">No Image</div>
                <?php } ?>

                <div class="card-content">

                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>

                    <p><?php echo htmlspecialchars($row['description']); ?></p>

                    <div class="card-footer">

                        <?php if ($row['price'] == 0) { ?>
                            <span class="free-badge">Free</span>
                        <?php } else { ?>
                            <span class="price"><?php echo $row['price']; ?></span>
                        <?php } ?>

                    </div>

                </div>

            </div>
        <?php } ?>

    </div>

</div>

</body>
</html>
``