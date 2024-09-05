<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="TickGhana is the leading event ticketing platform in Ghana, providing easy and secure access to tickets for your favorite events.">
    <meta name="keywords" content="event ticketing, buy tickets, events in Ghana, TickGhana, concert tickets, event booking">
    <meta name="author" content="TickGhana">
    <title>TickGhana - Your Event Ticketing Platform in Ghana</title>
    <?php include 'cdn.php'?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>
<?php include 'navbar.php'?>
<section>
    <div class="hero">
    <div class="swiper mySwiper">
    <div class="swiper-wrapper">
      <div class="swiper-slide">
        <div class="hero_text">
        <h1>Your Ultimate Solution for Hosting Unforgettable Events.</h1>
<p>A complete ticketing platform designed to manage events of all types and handle attendance seamlessly.</p>
        </div>
        <img src="./images/hero.jpg" alt="">
      </div>
    </div>
  </div>
    </div>
</section>


<?php include 'db.php'; ?>

<section>
    <div class="events_all">
        <div class="event_title">
            <h1>Featured events</h1>
        </div>
        <div class="events_grid">
            <?php
            $sql = "SELECT * FROM events";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '
                    <div class="event_card" onclick="location.href=\'event_details.php?id=' . $row['id'] . '\'">
                        <div class="event_image">
                            <img src="uploads/' . $row['image'] . '" alt="' . $row['event_title'] . '">
                        </div>
                        <div class="event_details">
                            <div class="event_info">
                                <div class="event_date">
                                    <p>' . date('M', strtotime($row['event_date'])) . '<br>' . date('d', strtotime($row['event_date'])) . '</p>
                                </div>
                                <div class="event_title">
                                    <h4>' . $row['event_title'] . '</h4>
                                </div>
                            </div>
                            <div class="event_bottom">
                                <div class="events_ussd">
                                    <p>Buy Ticket</p>
                                </div>
                                <div class="event_price">
                                    <p>GHS ' . $row['event_price'] . '</p>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<p>No events found</p>';
            }

            $conn->close();
            ?>
        </div>
    </div>
</section>

</body>
</html>

