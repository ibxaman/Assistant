<?php
require_once "config/database.php";
$id=(int)($_GET['id']??0);
$s=$conn->query("SELECT * FROM services WHERE id=$id")->fetch_assoc();
if(!$s){http_response_code(404);die('Service not found');}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=htmlspecialchars($s['service_name'])?> — MISA</title><link rel="stylesheet" href="assets/css/chat.css"></head><body>
<div style="max-width:850px;margin:auto;padding:28px"><a href="chat.php" class="back">← Back to MISA</a>
<h1><?=htmlspecialchars($s['service_name'])?></h1><p><?=htmlspecialchars($s['description'])?></p>
<div class="detail-box"><b>Procedure</b><p><?=nl2br(htmlspecialchars($s['service_procedure']))?></p>
<b>Fee</b><p><?=htmlspecialchars($s['fee'])?></p><b>Processing time</b><p><?=htmlspecialchars($s['processing_time'])?></p>
<b>Source</b><p><?=htmlspecialchars($s['source'])?></p></div></div></body></html>