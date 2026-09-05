<?php
require_once "config/database.php";
$res=$conn->query("SELECT * FROM services ORDER BY service_name");
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>MISA Services</title><link rel="stylesheet" href="assets/css/chat.css"></head><body>
<div style="max-width:1000px;margin:auto;padding:28px">
<a href="chat.php" class="back">← Back to MISA</a><h1>Service Directory</h1><p>Services currently available in the MISA knowledge base.</p>
<div class="service-list">
<?php while($r=$res->fetch_assoc()): ?>
<div class="service-card"><h2><?=htmlspecialchars($r['service_name'])?></h2><p><?=htmlspecialchars($r['description'])?></p>
<div class="service-meta"><b>Fee:</b> <?=htmlspecialchars($r['fee'])?> &nbsp; <b>Time:</b> <?=htmlspecialchars($r['processing_time'])?></div>
<a class="details" href="service-details.php?id=<?=(int)$r['id']?>">View details →</a></div>
<?php endwhile; ?>
</div></div></body></html>